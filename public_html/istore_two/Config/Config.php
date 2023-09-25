<?php

namespace Config;

class Config
{
    static public function data()
    {
        return [
            "REST_CERT_PATH" => dirname(__FILE__, 1) . "/AppleCare-Partner-0001054076.Prod.apple.com.chain.pem",
            "REST_SSL_KEY" => dirname(__FILE__, 1) . "/privatekey.pem",
            "REST_CERT_PASS" => "iStore07631Englewood",
            "REST_ACCOUNT_ID" => "service@istorestm.com",
            "REST_BASE_URL" => "https://api-partner-connect.apple.com",
            "REST_AUTH_TOKEN_APPLE" => "258c30f3-967a-46de-a9bc-d0975ee6c44p",
            "REST_AUTH_TOKEN" => "",
            "REST_SoldTo" => "0001103607",
            "REST_ShipTo" => "0001103607",
            "REST_AUTH_PATH" => "/api",
            "REST_GSX_PATH" => "/gsx/api",
            "REST_TABLE" => "istore_two",
        ];
    }

}
