<?php

function soapToJson($data)
{

	$xml = new SimpleXMLElement($data);
	$json = json_encode($xml);
	$arrResponse = json_decode($json, TRUE);

	return $arrResponse;
}
