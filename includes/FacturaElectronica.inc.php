<?php
class FacturaElectronica
{

  public function generar_token(){
    $curl = curl_init();  
    curl_setopt_array($curl, array(
      CURLOPT_URL => URL_FAC_ELECTRONICA . 'IntegrationAPI_2/api/login?username='.USER_FAC_ELECTRONICA.'&password='.PASS_FAC_ELECTRONICA,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
    ));
    curl_setopt($curl, CURLOPT_POSTFIELDS, array());
    $response = curl_exec($curl);
    curl_close($curl);
    $Token=str_replace('"',"",$response);
    return $Token;
  }

  public function factura($datos_factura,$NumeroResolucion,$Codigo,$IVA,$IDCiudad,$DiaSinIva){  

    if($DiaSinIva=="S") {
      $Porcentaje_Iva=0;
      $IVA=0;
    }
    else{
      $Porcentaje_Iva=$IVA*100;
    }
    
    $sql_cliente="SELECT IDCliente,EMail,Nombre,Apellido,Cedula,IDDepartamento,IDCiudad,Direccion,Celular,Telefono
                        FROM Cliente
                        WHERE IDCliente= '".$datos_factura["IDCliente"]."' ";
    $r_cliente=db_query($sql_cliente);
    $datos_cliente=db_fetch_array($r_cliente);

    
    if(!empty($NumeroResolucion) && !empty($Codigo) && (int)$datos_cliente["IDCliente"]>0){
         
        $Token=self::generar_token();
        if(!empty($Token)){
          //echo "continuo: " . $IDFactura." Num " . $Numerofactura . " PUNTO" . $IDPuntoVenta;          
          //Contruir json
          $json_factura=array();
          $response_factura=array();          
          
          //Datos Cliente

            if($IDCiudad==1){ //Bogota
              $SubdivisionCode="11";
              $SubdivisionName="Bogotá, D.C.";
              $CityCode="11001";
              $CityName="BOGOTÁ, D.C";
            }
            else{ //Medellin
              $SubdivisionCode="05";
              $SubdivisionName="Antioquia";
              $CityCode="05001";
              $CityName="MEDELLÍN";
            } 


            $array_customer_information=array();                  
            //$array_customer_information["IdentificationType"] = 13;
            $mystring = $datos_cliente["Cedula"];
            $findme   = '-';
            $pos = strpos($mystring, $findme);
            if ($pos === false) {
              $array_customer_information["IdentificationType"] = 13;
              $DV=null;
              $NumeroDoc= $datos_cliente["Cedula"];
          } else {
              $array_customer_information["IdentificationType"] = 31;
              $array_nit=explode("-",$mystring);
              $DV=$array_nit[1];
              $NumeroDoc= $array_nit[0];
          }    



            $array_customer_information["Identification"] = $NumeroDoc;
            $array_customer_information["DV"] = $DV;
            $array_customer_information["RegistrationName"] = $datos_cliente["Nombre"] . " " .$datos_cliente["Apellido"];
            $array_customer_information["CountryCode"] = "CO";
            $array_customer_information["CountryName"] = "Colombia";
            $array_customer_information["SubdivisionCode"] = $SubdivisionCode;
            $array_customer_information["SubdivisionName"] = $SubdivisionName;
            $array_customer_information["CityCode"] = $CityCode;
            $array_customer_information["CityName"] = $CityName;
            $array_customer_information["AddressLine"] = $datos_cliente["Direccion"]; 

             if(!empty($datos_cliente["Celular"]))
              $Telefono=trim($datos_cliente["Celular"]);
             else 
              $Telefono=trim($datos_cliente["Telefono"]);

              if($Telefono=="0" || empty($Telefono) ){
                $Telefono="3701266";
              }

              
                

            $array_customer_information["Telephone"] = $Telefono;

            if (!filter_var(trim($datos_cliente["EMail"]), FILTER_VALIDATE_EMAIL)) {
              $datos_cliente["EMail"]="";
            }
          
            if(!empty($datos_cliente["EMail"])){
              $CorreoCliente=$datos_cliente["EMail"].";".EMAIL_FAC_ELECTRONICA;
            }
            else{
              $CorreoCliente=EMAIL_FAC_ELECTRONICA;
            }

            $array_customer_information["Email"] = $CorreoCliente;
            $array_customer_information["CustomerCode"] = "";
            $array_customer_information["AdditionalAccountID"] = 1;
            $array_customer_information["TaxLevelCodeListName"] = "48";
            $array_customer_information["PostalZone"] = "000000";
            $array_customer_information["TaxSchemeCode"] = "01";
            $array_customer_information["TaxSchemeName"] = "IVA";
            $array_customer_information["FiscalResponsabilities"] = "O-13;O-15";
            $array_customer_information["PartecipationPercent"] = 100.0;
            $array_customer_information["AdditionalCustomer"] = array();
            $array_customer_information["DoNotToSentEmailBody"] = false;      
            $json_factura["CustomerInformation"] = $array_customer_information;
          //FIN Datos Cliente

          //Datos factura
            $array_invoice_general_information=array();   
            $array_invoice_general_information["InvoiceAuthorizationNumber"] = (string)$NumeroResolucion;      
            $array_invoice_general_information["PreinvoiceNumber"] = (int)$datos_factura["NumeroFactura"];
            //$array_invoice_general_information["InvoiceNumber"] = $Codigo.$datos_factura["NumeroFactura"];
            $array_invoice_general_information["InvoiceNumber"] = $datos_factura["NumeroFactura"];

            if($datos_factura["Descuento"]==6 || $datos_factura["NumeroPagare"]!="")
              $DiasOff=75;
            else
              $DiasOff=0;


            //Si tiene descuentos por bono de fidelizacion
            $datos_factura["ValorBono"];
            if((int)$datos_factura["ValorBono"]>0){
              $ValorBonos=$datos_factura["ValorBono"];
              $ComentarioBono="Se aplica bono por valor de $".$datos_factura["ValorBono"];

              $datos_factura["ValorBono"]=0;
            }
            else{
              $ComentarioBono="";
            }
            

            $array_invoice_general_information["DaysOff"] = $DiasOff;      
            $array_invoice_general_information["Currency"] = "COP";  
            $array_invoice_general_information["ExchangeRate"] = 0.0;
            $array_invoice_general_information["ExchangeRateDate"] = "0001-01-01T00:00:00";
            $array_invoice_general_information["CustomizationID"] = "10";
            $array_invoice_general_information["SchemeID"] = null;
            //vendedor
            $array_invoice_general_information["SalesPerson"] = $datos_factura["Nombre"] . " " . $datos_factura["Apellidos"];

            if($DiaSinIva=="S")
              $Note="Bienes cubiertos";
            else{
              $Note="";
            }  
            $array_invoice_general_information["Note"] = $Note;
            $array_invoice_general_information["ExternalGR"] = false;
            $array_invoice_general_information["InvoiceDueDate"]= null;
            $array_invoice_general_information["StartDateTime"] = "0001-01-01T00:00:00";
            $array_invoice_general_information["EndDateTime"] = "0001-01-01T00:00:00";
            $array_invoice_general_information["IsContingency"] =false;      
            $json_factura["InvoiceGeneralInformation"] = $array_invoice_general_information;
          //FIN Datos factura

          //Datos Interoperabilidad
            
            //GROUP
            $array_group=array();
            $response_collection=array();
            $response_additional_information=array();
            $array_group["GroupSchemeName"]=null;
            $array_group["CollectionSchemeName"]=null;
            $array_collection=array();
            $array_adicional=array("Name"=>null,"Value"=>null,"SchemeID"=>null,"SchemeName"=>null);
            array_push($response_additional_information, $array_adicional);
            $array_collection["AdditionalInformation"]=$response_additional_information;
            array_push($response_collection, $array_collection);
            $array_group["Collection"]=$response_collection;
            $array_interoperabilidad["Group"] =$array_group;
            
            //URLDescargaAdjuntos        
            $array_adjuntos=array();        
            $response_parametros_argumentos=array();
            $array_adjuntos["URL"]=null;
            $array_argumentos=array();
            $array_argumentos=array("Name"=>null,"Value"=>null);
            array_push($response_parametros_argumentos, $array_argumentos);
            $array_adjuntos["ParametrosArgumentos"]=$response_parametros_argumentos;      
            $array_interoperabilidad["URLDescargaAdjuntos"] =$array_adjuntos;  

            //EntregaDocumento        
            $array_entrega_doc=array();        
            $response_parametros_argumentos=array();
            $array_entrega_doc["WS"]=null;
            $array_argumentos=array();
            $array_argumentos=array("Name"=>null,"Value"=>null);
            array_push($response_parametros_argumentos, $array_argumentos);
            $array_entrega_doc["ParametrosArgumentos"]=$response_parametros_argumentos;      
            $array_interoperabilidad["EntregaDocumento"] =$array_entrega_doc; 

            $json_factura["Interoperabilidad"] = $array_interoperabilidad;
          //FIN Datos Interoperabilidad

          //Person
            $array_person=array();   
            $array_person["IdentificationType"] =null;      
            $array_person["Identification"]= null;
            $array_person["FirstName"]= null;
            $array_person["FamilyName"]= null;
            $array_person["NameIdentification"]= null;
            $array_person["IssuerPartyName"]= null;
            $array_person["CityCode"]= null;
            $array_person["CityName"]= null;
            $array_person["IssuerCountryName"]= null;
            $array_person["ResidenceCityCode"]= null;
            $array_person["ResidenceCityName"]=null;
            $array_person["ResidenceAddressLine"]= null;
            $array_person["ResidenceCountryName"]= null;
            $json_factura["Person"] = $array_person;
          //FIN Person

          //BillingReference
            $array_billing_reference=array();
            $array_invoice_document_reference=array();
            $response_invoice_document_reference=array();
            $array_invoice_document_reference["IdentificationType"]= null;
            $array_invoice_document_reference["Identification"]= null;
            $array_invoice_document_reference["CodigoPrestadorSS"]= null;
            $array_invoice_document_reference["AuthorizationID"]= null;
            $array_invoice_document_reference["ReferenceID"]= null;
            $array_invoice_document_reference["Cufe"]= null;
            $array_invoice_document_reference["IssueDateInvoice"]= "0001-01-01T00:00:00";
            $array_invoice_document_reference["CustomizationIDInvoice"]= null;
            $array_invoice_document_reference["NameCustomizationIDInvoice"]= null;
            $array_invoice_document_reference["Amount"]= 0.0;
            $array_invoice_document_reference["Type"]= null;        
            $array_billing_reference["InvoiceDocumentReference"]=$array_invoice_document_reference;      
            array_push($response_invoice_document_reference, $array_billing_reference);
            $json_factura["BillingReference"] = $response_invoice_document_reference;
          //FIN BillingReference

          //BuyerCustomerParty
          $array_buyer=array();   
          $array_buyer["CustomerAssignedAccountID"] =null;      
          $array_buyer["SupplierAssignedAccountID"]= null;
          $array_buyer["AdditionalAccountID"]= null;      
          $json_factura["BuyerCustomerParty"] = $array_buyer;
        //FIN BuyerCustomerParty

        //Delivery
          $array_delivery=array();   
          $array_delivery["AddressLine"]= "";
          $array_delivery["CountryCode"]="";
          $array_delivery["CountryName"]= "";
          $array_delivery["SubdivisionCode"]= "";
          $array_delivery["SubdivisionName"]= "";
          $array_delivery["CityCode"]= "";
          $array_delivery["CityName"]= "";
          $array_delivery["ContactPerson"]= "";
          $array_delivery["DeliveryDate"]= "0001-01-01T00:00:00";
          $array_delivery["DeliveryCompany"]= "";
          $array_delivery["IdentificationConveyor"]= null;
          $array_delivery["DVConveyor"]= null;
          $json_factura["Delivery"] = $array_delivery;
      //FIN Delivery

      //AdditionalDocuments
        $array_additional_documents=array();   
        $array_additional_documents["OrderReference"]= "";
        $array_additional_documents["OrderReferenceIssueDate"]="0001-01-01T00:00:00";
        $array_additional_documents["DespatchDocumentReference"]= "";
        $array_additional_documents["DespatchDocumentIssueDate"]= "0001-01-01T00:00:00";
        $array_additional_documents["ReceiptDocumentReference"]= "";
        $array_additional_documents["ReceiptDocumentIssueDate"]= "0001-01-01T00:00:00";
        $array_additional_documents["AdditionalDocument"]= array();
        $json_factura["AdditionalDocuments"] = $array_additional_documents;
      //FIN AdditionalDocuments

      //AdditionalDocumentReceipt
        $array_additional_document_receipt=array();
        $response_additional_document_receipt=array();   
        $array_additional_document_receipt["DocumentValue"]= "";
        $array_additional_document_receipt["IssueDate"]="0001-01-01T00:00:00";
        array_push($response_additional_document_receipt, $array_additional_document_receipt);
        $json_factura["AdditionalDocumentReceipt"] = $response_additional_document_receipt;
      //FIN AdditionalDocumentReceipt
      //AdditionalProperty
          $array_additional_property=array();
          $response_additional_property=array();   
          $array_additional_property["Name"]= "Son:";
          $array_additional_property["Value"]="";
          array_push($response_additional_property, $array_additional_property);
          //$json_factura["AdditionalProperty"] = $response_additional_property;
          $json_factura["AdditionalProperty"] = array();
      //FIN AdditionalProperty

      //PaymentSummary            
        $sql_forma_pago="SELECT IDFormaPago FROM FormaPagoFactura WHERE IDFactura = '".$datos_factura["IDFactura"]."' and IDPuntoVenta = '".$datos_factura["IDPuntoVenta"]."' ";
        $r_forma_pago=db_query($sql_forma_pago);
        while($row_forma_pago=db_fetch_array($r_forma_pago)){

          switch($row_forma_pago["IDFormaPago"]){
            case "1":
              $MedioPago=10;
            break; 
            case "3":
            case "4": 
            case "5":
            case "6":
            case "18":
            case "19":
            case "21":
              $MedioPago=48;
            break; 
            case "7":
            case "8":            
              $MedioPago=49;
            break; 
            default:
              $MedioPago=10;
          }
        }  
        $array_payment_summary=array();   
        $array_payment_summary["PaymentType"]= "1";
        $array_payment_summary["PaymentMeans"]=$MedioPago;
        $array_payment_summary["PaymentNote"]= $ComentarioBono;    
        $json_factura["PaymentSummary"] = $array_payment_summary;
      //FIN PaymentSummary
      
      
      //ItemInformation
        $array_item_information=array();
        $response_item_information=array();   
        
        $array_additional_property=array();
        $response_additional_property=array();   

        $array_taxes=array();
        $response_taxes=array(); 
        
        //Consulto el detalle de la factura
        $contador_productos=0;
        $ValorBonoAplicado="N";
        $sql_detalle="SELECT * FROM DetalleFactura WHERE IDFactura = '".$datos_factura["IDFactura"]."' and IDPuntoVenta = '".$datos_factura["IDPuntoVenta"]."' ";
        $_detalle=db_query($sql_detalle);
        while($row_factura_detalle=db_fetch_array($_detalle)){

          $sql_cod_esp="SELECT IDPuntoVentaReferencia, T.IDTalla, T.Nombre as NombreTalla
												 FROM CodificacionEspecifica CE, Talla T
												 WHERE CE.IDTalla=T.IDTalla and  IDCodificacionEspecifica = '".$row_factura_detalle["IDCodificacionEspecifica"]."' ";
			    $qry_cod_esp = db_query($sql_cod_esp);
				  $row_cod_esp = db_fetch_array($qry_cod_esp);

          $pto_vta_ref = $row_cod_esp["IDPuntoVentaReferencia"];
				  $id_ref = get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",$pto_vta_ref);
				  $sql_referencia="SELECT TR.Descripcion Categoria, TT.Descripcion Genero, R.Numero Referencia, Color.DescripcionLarga as ColorReferencia
												 FROM TipoReferencia TR, TipoTalla TT, Referencia R, Color
												 WHERE R.IDTipoReferencia=TR.IDTipoReferencia and R.IDTipoTalla=TT.IDTipoTalla and Color.IDColor=R.IDColor and IDReferencia = '".$id_ref."' ";
			    $qry_ref = db_query($sql_referencia);
          
				  while ($row_ref = db_fetch_array($qry_ref)){            
            
            $contador_productos++;
					  $Genero= $row_ref["Genero"];
					  $Referencia= $row_ref["Referencia"];
            $DescripcionReferencia= utf8_encode($row_ref["Categoria"]);
            $Color= $row_ref["ColorReferencia"];
            $Talla= $row_cod_esp["NombreTalla"];
					  $PrecioU= $row_factura_detalle["PrecioU"];
					  $ValorU= $row_factura_detalle["ValorU"];

            
            if($PrecioU>=$ValorBonos && $ValorBonoAplicado=="N" && $ValorBonos>0){
              if($IVA==0){
                $ValorU=($PrecioU-$ValorBonos)/(($IVA+1));
              }
              else{
                $ValorU=($PrecioU-$ValorBonos)/(($IVA+1));
              }              
              $row_factura_detalle["ValorU"]=$ValorU;
              $ValorBonoAplicado="S";
            }
            
            

            if($row_factura_detalle["DescuentoPar"]>0){
              $row_factura_detalle["ValorU"] = $row_factura_detalle["ValorU"] - ($row_factura_detalle["ValorU"] * $row_factura_detalle["DescuentoPar"] /100);
            }


            
            
            $array_item_information["ItemReference"]= $Referencia;
            $array_item_information["Name"]= $DescripcionReferencia;
            $array_item_information["SSIdentification"]= null;
            $array_item_information["SSDescription"]= null;
            $array_item_information["SSAuthorizationID"]= null;
            $array_item_information["SSCodigoPrestador"]= null;
            $array_item_information["Quatity"]= (int)$row_factura_detalle["Cantidad"];
            $array_item_information["Price"]= round((float)$row_factura_detalle["ValorU"],0);
            $Total=(int)$row_factura_detalle["Cantidad"]*round((float)$row_factura_detalle["ValorU"],0);
            $GranTotal+=$Total;

            if($DiaSinIva=="S") {
              $TotalConIva=$Total;
            }
            else{
              $TotalConIva=$Total + ($Total*19/100);
            } 
            
            $GranTotalConIva+=$TotalConIva;
            $TotalIva=round((float)$row_factura_detalle["ValorU"],0)."*".$IVA;;
            $TotalIva=round( (float)$row_factura_detalle["ValorU"],0  )*$IVA;
            $TotalIva= $TotalIva * (int)$row_factura_detalle["Cantidad"];
            $GranTotalIva+=$TotalIva;
            $array_item_information["LineExtensionAmount"]= round((float)$Total,0);
            $array_item_information["LineTotal"]= (float)$TotalConIva;
            $array_item_information["LineAllowanceTotal"]= 0.0;
            $array_item_information["LineChargeTotal"]= 0.0;
            $array_item_information["LineTotalTaxes"]= (float)$TotalIva;
            $array_item_information["MeasureUnitCode"]= "94";
            $array_item_information["FreeOFChargeIndicator"]= false;
            $array_item_information["Equals10Percent"]= false;
            $array_item_information["AdditionalReference"]= array();
            $array_item_information["Nota"]= null;
            $array_additional_property=array();
            $response_additional_property=array();
            $array_taxes=array();
            $response_taxes=array();
            //Info adicional
            $array_additional_property["Name"]= "Color";
            $array_additional_property["Value"]= utf8_encode($Color);
            array_push($response_additional_property, $array_additional_property);
            $array_additional_property["Name"]= "Talla";
            $array_additional_property["Value"]= $Talla;
            array_push($response_additional_property, $array_additional_property);
            $array_item_information["AdditionalProperty"]= $response_additional_property;
            //Taxes Information
            $array_taxes["Id"]= "01";
            $array_taxes["TaxEvidenceIndicator"]= false;
            $array_taxes["TaxableAmount"]= round((float)$Total,0);
            $array_taxes["TaxAmount"]= (float)$TotalIva;
            $array_taxes["Percent"]= round((float)$Porcentaje_Iva,0);
            $array_taxes["BaseUnitMeasure"]= "0";
            $array_taxes["PerUnitAmount"]= 0.0;
            array_push($response_taxes, $array_taxes);
            $array_item_information["TaxesInformation"]= $response_taxes;

              


            if((int)$datos_factura["ValorBono"]>0){
              /*
              $response_desc=array();
              $array_desc=array();
              $array_desc["Id"]= 1;
              $array_desc["ChargeIndicator"]= false;
              $array_desc["AllowanceChargeReason"]= "Descuento de temporada";
              $array_desc["MultiplierFactorNumeric"]= 0.0;
              $array_desc["Amount"]= (int)$datos_factura["ValorBono"];
              $array_desc["BaseAmount"]= (float)$TotalConIva;;            
              array_push($response_desc, $array_desc);
              $array_item_information["AllowanceCharge"]= $response_desc;
              */
              $array_item_information["AllowanceCharge"]= array();
            }
            else{
              $array_item_information["AllowanceCharge"]= array();
            }

            
            array_push($response_item_information, $array_item_information);
				  }



        }


        if($contador_productos>=1){

          $json_factura["ItemInformation"] = $response_item_information;
        //FIN AdditionalDocumentReceipt
       

       

      //InvoiceTaxTotal
        $array_invoice_taxTotal=array();
        $response_invoice_taxTotal=array();   
        $array_invoice_taxTotal["Id"]= "01";
        $array_invoice_taxTotal["TaxEvidenceIndicator"]= false;
        $array_invoice_taxTotal["TaxableAmount"]= round((float)$GranTotal,0);
        $array_invoice_taxTotal["TaxAmount"]= (float)$GranTotalIva;
        $array_invoice_taxTotal["Percent"]= round((float)$Porcentaje_Iva,1);
        $array_invoice_taxTotal["BaseUnitMeasure"]= "0";
        $array_invoice_taxTotal["PerUnitAmount"]= 0.0;
        array_push($response_invoice_taxTotal, $array_invoice_taxTotal);
        $json_factura["InvoiceTaxTotal"] = $response_invoice_taxTotal;
      //FIN InvoiceTaxTotal

      $json_factura["InvoiceTaxOthersTotal"] = null;


      
      if((int)$datos_factura["ValorBono"]>0){
        
        $response_desc=array();
        $array_desc=array();
        $array_desc["Id"]= 1;
        $array_desc["ChargeIndicator"]= false;
        $array_desc["AllowanceChargeReason"]= "Descuento de temporada";
        $array_desc["MultiplierFactorNumeric"]= 0.0;
        $array_desc["Amount"]= (int)$datos_factura["ValorBono"];
        $array_desc["BaseAmount"]= (float)$GranTotalConIva;;            
        array_push($response_desc, $array_desc);
        $json_factura["InvoiceAllowanceCharge"] = $response_desc;        
      }
      else{
        $json_factura["InvoiceAllowanceCharge"] = array();
      }

      

      //InvoiceTotal
        $array_invoice_total=array();   
        $array_invoice_total["LineExtensionAmount"]= round((float)$GranTotal,0);
        $array_invoice_total["TaxExclusiveAmount"]=round((float)$GranTotal,0);
        $array_invoice_total["TaxInclusiveAmount"]= (float)$GranTotalConIva;
        $array_invoice_total["AllowanceTotalAmount"]= (int)$datos_factura["ValorBono"];
        $array_invoice_total["PrepaidAmount"]= 0.0;
        $array_invoice_total["ChargeTotalAmount"]= 0.0;
        $array_invoice_total["PayableAmount"]= (float)$GranTotalConIva-(int)$datos_factura["ValorBono"];
        $json_factura["InvoiceTotal"] = $array_invoice_total;
      //FIN InvoiceTotal

        $json_factura["Documents"] = null;

          array_push($response_factura, $json_factura);
          $json_envio=json_encode($json_factura);

          
          //echo $json_envio;
          //exit;

          //Ejecuto llamado WS
              $curl = curl_init();
              curl_setopt_array($curl, array(
                CURLOPT_URL => URL_FAC_ELECTRONICA . 'integrationAPI_2/api/insertinvoice?SchemaID='.SchemaID_FAC_ELECTRONICA.'&IDNumber='.IDNumber_FAC_ELECTRONICA.'&TemplateID='.TemplateID_FAC_ELECTRONICA,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',            
              ));

              curl_setopt_array($curl, array(
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $json_envio,
                CURLOPT_HEADER => false,
                CURLOPT_HTTPHEADER => array('Content-Type:application/json', 'Content-Length: ' . strlen($json_envio), 'Authorization: misfacturas  '.$Token)
              ));

              $response = curl_exec($curl);
              curl_close($curl);
              $datos_respuesta=json_decode($response);          
              $resultado=$datos_respuesta->Message;
              $resultado=str_replace("'"," ",$resultado);          
              if(!empty($datos_respuesta->DocumentId)){
                $resultado=$datos_respuesta->MessageValidation;
                $sql_actualiza="UPDATE Factura SET FacturaElectronica = 'S' WHERE IDFactura='".$datos_factura["IDFactura"]."' and NumeroFactura = '".$datos_factura["NumeroFactura"]."' and  IDPuntoVenta = '".$datos_factura["IDPuntoVenta"]."' ";
                db_query($sql_actualiza);
              }

              echo $response;
          //FIN EJECUTAR LLAMADO
          

         
          
        }
        else{         
          $resultado="Factura sin productos";
        }

        }
        else{          
          $resultado= "Token invalido";
        }
    }
    else{      
      $resultado= "Sin resolucion, codigo tienda o datos cliente";
    }

      
      $sql_log="INSERT INTO LogFacturaElectronica (IDFactura,IDPuntoVenta,NumeroFactura,Resultado,FechaTrCr) 
                VALUES ('".$datos_factura["IDFactura"]."','".$datos_factura["IDPuntoVenta"]."','".$datos_factura["NumeroFactura"]."','".$resultado."',NOW())";
      db_query($sql_log);           
      //exit;
  } // fin function



  
}
