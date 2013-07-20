<?php

require_once "instagram.class.php";
include $_SERVER['DOCUMENT_ROOT']."/keychain.php";

function instagramMediaIdFromLink($link) {
	$curl = curl_init();
	curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => "http://api.instagram.com/oembed?url=$link",
		));


	$responce = curl_exec($curl);
	curl_close($curl);

	$data = json_decode($responce);
	return $data->media_id;
}

function instagramPrintFromMediaData($media) {

	/* $url =  $_SERVER['DOCUMENT_ROOT']."/engine/generate?"; */
	$url = 'http://print.jonathanlking.com/engine/generate?';
	$data =  array(
		'username' => $media->data->user->username,
		'profilePictureURL' => $media->data->user->profile_picture,
		'photoURL' => $media->data->images->standard_resolution->url,
		'creationTime' => $media->data->created_time,
		'location' => $media->data->location->name,
		'caption' => $media->data->caption->text,
		'likes' => $media->data->likes->count,
		'link' => $media->data->link,
		'logo' => ""
	);

	$request = $url.http_build_query($data);

	$curl = curl_init();
	curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $request,
		));


	$responce = curl_exec($curl);
	curl_close($curl);

	return $responce;
}

$keychain = new keychain;
$clientID = $keychain->getInstagramClientId();

$link = $_REQUEST["link"];
if (empty($link)) die();

// Get the Instagram media id from the link
$mediaId = instagramMediaIdFromLink($link);
if (empty($mediaId)) die();

// Get the image details from the media_id
$instagram = new Instagram($clientID);
$mediaData = $instagram->getMedia($mediaId);

$print = instagramPrintFromMediaData($mediaData);

if ($_REQUEST["format"] === "base64") echo base64_encode($print);
else echo $print;

?>
