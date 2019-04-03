<?php

/**
 * User: Lucas Quintela Miranda
 * Email: lucas.quintela@resutlate.com.br
 * Date: 09/05/18
 * Time: 12:34
 */
class Resultate_Cachecloudflare_Model_Api
{
    private $zoneID;
    private $authEmail;
    private $authKey;


    private function _setAuthData($website = false)
    {
        $this->zoneID = $this->_getWebsiteConfig('cdn_config/options/zone_id', $website);
        $this->authEmail = $this->_getWebsiteConfig('cdn_config/options/auth_email', $website);
        $this->authKey = $this->_getWebsiteConfig('cdn_config/options/auth_key', $website);

        if(!($this->zoneID && $this->authEmail && $this->authKey)){
            Mage::throwException('Módulo não configurado');
        }
    }

    public function cleanAllCache($website)
    {
        if (!$website) {

            Mage::throwException('Selecione uma opção');

        } elseif ($website == 'all') {

            foreach (Mage::app()->getWebsites() as $websiteData) {

                $this->_setAuthData($websiteData->getId());
                $response = $this->_postData('{"purge_everything": true}');
                if (!$response['success']) {
                    $error_msg = "";
                    foreach ($response['errors'] as $error) {
                        $error_msg .= "<p> <strong> #" . $error['code'] . " -> </strong> " . $error['message'] . "</p>";
                    }
                    if ($error_msg) {
                        Mage::throwException($error_msg);
                    } else {
                        Mage::throwException("Erro ao liberar Cache");
                    }
                }

            }

            return Mage::helper('resultate_cachecloudflare')->__('Cache Liberado com Sucesso!');

        } else {
            $this->_setAuthData($website);
            $response = $this->_postData('{"purge_everything": true}');
            if ($response['success']) {
                return Mage::helper('resultate_cachecloudflare')->__('Cache Liberado com Sucesso!');
            } else {
                $error_msg = "";
                foreach ($response['errors'] as $error) {
                    $error_msg .= "<p> <strong> #" . $error['code'] . " -> </strong> " . $error['message'] . "</p>";
                }
                if ($error_msg) {
                    Mage::throwException($error_msg);
                } else {
                    Mage::throwException("Erro ao liberar Cache");
                }
            }
        }

    }

    public function cleanUrlCache($url = false, $website = false)
    {
        if (!$url) {
            Mage::throwException('URL é Obrigatório');
        }
        if (!$website) {
            Mage::throwException('Selecione uma opção');
        }

        $this->_setAuthData($website);
        $response = $this->_postData('{"files": ["' . $url . '"]}');

        if ($response['success']) {
            return Mage::helper('resultate_cachecloudflare')->__('Cache Liberado com Sucesso!');
        } else {
            $error_msg = "";
            foreach ($response['errors'] as $error) {
                $error_msg .= "<p> <strong> #" . $error['code'] . " -> </strong> " . $error['message'] . "</p>";
            }
            if ($error_msg) {
                Mage::throwException($error_msg);
            } else {
                Mage::throwException("Erro ao liberar Cache");
            }
        }


    }

    private function _postData($body = false)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.cloudflare.com/client/v4/zones/" . $this->zoneID . "/purge_cache",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => array(
                "content-type: application/json",
                "x-auth-email: " . $this->authEmail,
                "x-auth-key: " . $this->authKey
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            Mage::throwException("cURL Error #:" . $err);
        } else {
            return json_decode($response, true);
        }

    }

    private function _getWebsiteConfig($path, $websiteId = false)
    {
        if (!$websiteId) {
            return Mage::getStoreConfig('cdn_config/options/zone_id');
        } else {
            return Mage::app()->getWebsite($websiteId)->getConfig($path);
        }
    }
}