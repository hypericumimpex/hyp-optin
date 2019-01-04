<?php
class Mpp_Infusionsoft_FileServiceBase extends Mpp_Infusionsoft_Service{

    public static function getFile($fileId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $fileId
        );

        return parent::send($app, "FileService.getFile", $params);
    }

    public static function getDownloadUrl($fileId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $fileId
        );

        return parent::send($app, "FileService.getDownloadUrl", $params);
    }

    public static function uploadFile($contactId, $fileName, $base64encoded, Mpp_Infusionsoft_App $app = null){
        // If contactId is null, do not include it in the params
        if ($contactId === null) {
            $params = array(
                $fileName,
                $base64encoded
            );
        } else {
            $params = array(
                (int) $contactId,
                $fileName,
                $base64encoded
            );
        }

        return parent::send($app, "FileService.uploadFile", $params);
    }

    public static function replaceFile($fileId, $base64encoded, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $fileId,
            $base64encoded
        );

        return parent::send($app, "FileService.replaceFile", $params);
    }

    public static function renameFile($fileId, $fileName, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $fileId,
            $fileName
        );

        return parent::send($app, "FileService.renameFile", $params);
    }

}
