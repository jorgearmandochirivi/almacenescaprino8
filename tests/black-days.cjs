const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const source = fs.readFileSync(__dirname + '/../Movimiento/Factura.php', 'utf8');
const fn = source.slice(source.indexOf('function aplicar_black_days('), source.indexOf('function calculatotal('));
function cart(rows, puntoVenta = 17) {
  const elements = {ObservacionDescuento: {value: ''}};
  rows.forEach((row, i) => {
    const values = {Numero: 'ZA100', Talla: '37', Cantidad: '1', Precio: '100000', DescuentoLin: '', ...row};
    for (const [key, value] of Object.entries(values)) elements[key + (i + 1)] = {value, style: {background: ''}};
  });
  const ctx = {document: {frm: {IDPuntoVenta: {value: puntoVenta}, ITEM: {value: rows.length}, elements}}, Intl, Date};
  vm.createContext(ctx);
  vm.runInContext(fn, ctx);
  return {elements, run: date => ctx.aplicar_black_days(date)};
}
for (const [qty, rate] of [[1,10],[2,15],[3,20],[4,20]]) {
  const c = cart([{Cantidad: qty}]);
  c.run('2026-11-26');
  assert.equal(c.elements.DescuentoLin1.value, rate);
  c.run('2026-11-30');
  assert.equal(c.elements.DescuentoLin1.value, rate, 'recalculation must not compound');
  c.run('2026-12-01');
  assert.equal(c.elements.DescuentoLin1.value, '');
}
for (const ref of ['ZC123','zc45','ZV11NE','ZV12','ZV13','ZV14','Zv60CF','ZV61','ZV62','TARJETA','CREPUL','ZWP1','ZSE1****']) {
  const c = cart([{}, {Numero: ref, Cantidad: 5}]);
  c.run('2026-11-28');
  assert.equal(c.elements.DescuentoLin1.value, 10, ref + ' must not count');
  assert.equal(c.elements.DescuentoLin2.value, '', ref + ' must not receive discount');
}
const mixed = cart([{}, {Numero:'ZV15NE'}, {Numero:'BOLSO', Talla:'1', Cantidad:4}]);
mixed.run('2026-11-28');
assert.equal(mixed.elements.DescuentoLin1.value,15);
assert.equal(mixed.elements.DescuentoLin2.value,15);
assert.equal(mixed.elements.DescuentoLin3.value,'');
mixed.elements.Cantidad2.value = 0;
mixed.run('2026-11-28');
assert.equal(mixed.elements.DescuentoLin1.value,10);
assert.equal(mixed.elements.DescuentoLin2.value,'');
for (const date of ['2026-11-25','2026-12-01','2027-11-26']) {
  const c = cart([{}]);
  assert.equal(c.run(date),false);
  assert.equal(c.elements.DescuentoLin1.value,'');
}
console.log('Black Days: cantidades, fechas, exclusiones y recálculo correctos.');

for (const store of [6, '6', 16, '16', 5, 7, 15, 17, 60, 160, 24000]) {
  for (const date of ['2026-11-23', '2026-11-24', '2026-11-25', '2026-11-26', '2026-11-30', '2026-12-01']) {
    const c = cart([{}], store);
    const active = ![6, 16].includes(Number(store)) && date >= '2026-11-26' && date <= '2026-11-30';
    assert.equal(c.run(date), active, `almacén ${store}, fecha ${date}`);
    assert.equal(c.elements.DescuentoLin1.value, active ? 10 : '');
  }
}
console.log('Black Days: almacenes 6 y 16 excluidos; demás activos del 26 al 30.');
