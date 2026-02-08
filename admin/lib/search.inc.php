<?php

function makeboolean($sqlfieldname, $keywordstr) {

	$keywordstr=str_replace('\"','',$keywordstr);
	$keywordstr=preg_replace("/([[:space:]]{2,})/",' ',$keywordstr);
	$keyword = $keywordstr;
	
	$keyword = strtolower($keyword);

	$keyword = str_replace(" and ","+",$keyword);
	$keyword = str_replace(" or ","|",$keyword);
	$keyword = str_replace(" not ","-",$keyword);
	 
	$keyword = preg_replace("/[\$\%\!\@\#\^\&\*\(\)\=\~\`\?\{\}\'\:\'\;\<\>]/","",$keyword);
	
	if($keyword && ($keyword !="+" && $keyword !="-" && $keyword !="|") ){
	
	$keyword = trim($keyword);
	$keyword = str_replace(" ","+",$keyword);
	
	
		$operatorcount = 0;
		$len = strlen($keyword);
		for ($z = 0; $z < $len; $z++) {
			if(($keyword[$z] == "+") || ($keyword[$z] == "|") || ($keyword[$z] == "-")) {
				$operatorpos[$operatorcount] = $z;
				$operatorcount++;
			}
		} 
	
		if ($operatorcount != 0) { 
		for ($z = 0; $z < $operatorcount; $z++) {
			if($z == 0) {
				$startpos = 0;
				$endpos = $operatorpos[$z];
			} else {
				$startpos = $operatorpos[$z - 1] + 1;
				$endpos = $operatorpos[$z];
			}
			 
			$word = $endpos - $startpos;
			$keystring = substr($keyword,$startpos,$word);
			$keystring = str_replace("(","",$keystring);
			$keystring = str_replace(")","",$keystring);
			$keywords[$z] = $keystring;
			$operator_pos = $operatorpos[$z];
			$operators[$z] = $keyword[$operator_pos];
		} // end the for loop
	
		$wordcount = $operatorcount + 1;
		$startpos = $operatorpos[$z - 1] + 1;
		$len2 = strlen($keyword) - $startpos;
		$linestr = substr($keyword,$startpos,$len2);


		//store the line into the keywords array
		$keywords[$wordcount - 1] = $linestr;
	
		for ($z=0; $z < $wordcount; $z++) {
			$replacekeyword = $keywords[$z];

			$y = $z -1;
				if ($operators[$y] != "-")   //odd case is in a NOT...must do something different!
					$keyword = str_replace($replacekeyword,"$sqlfieldname LIKE '%$replacekeyword%'",$keyword);
				else
					$keyword = str_replace($replacekeyword,"$sqlfieldname NOT LIKE '%$replacekeyword%'",$keyword);
		}
	
		$keyword = str_replace("+"," AND ", $keyword);
		$keyword = str_replace("|"," OR ", $keyword);
		$keyword = str_replace("-"," AND ", $keyword);  //I fudged in the above statement so this possible :-)
	
		} // end if operatorcount != 0
		else { //there were no operators in the string
			$replacekeyword = $keyword;
			if ($keyword != "") {
				$keyword = str_replace($replacekeyword," $sqlfieldname LIKE '%$replacekeyword%' ",$keyword);  
			}
		}
	}//end if($keyword)
	else{
		$keyword=$sqlfieldname;
	}//end else if($keyword)
	return($keyword);
	
}  //end the makebooleanstatement function
?>