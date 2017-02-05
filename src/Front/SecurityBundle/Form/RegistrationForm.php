<?php

namespace Front\SecurityBundle\Form;
use Front\CoreBundle\Helper\Flash\FlashHelperInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Created by PhpStorm.
 * User: ovidiu
 * Date: 19.01.2017
 * Time: 17:37
 */
class RegistrationForm
{
    private $flashHelper;
    private $params;

    public function __construct(FlashHelperInterface $flashHelper, $params)
    {
        $this->flashHelper = $flashHelper;
        $this->params = $params;
    }

    public function submit(Request $request){

        $response = [];

        try{
            // Create a client with a base URI
            $client = new Client(['base_uri' => $this->params["api_address"]]);
            $post  = $client->request("POST", 'account/register/', [
                "form_params"=> $request->request->get("register"),
                "headers"=>[
                    "Accept" =>"application/json"
                ]
            ]);

            if($post->getStatusCode() == Response::HTTP_OK && $post->hasHeader('Content-Length')){
                $body = (string) $post->getBody()->getContents();
                $response = \GuzzleHttp\json_decode($body , true);
                $this->flashHelper->addSuccess($body);
            }

        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                if($e->getResponse()->getStatusCode() == Response::HTTP_OK){
                    $body =   (string)($e->getResponse()->getBody()->getContents());
                    $response = \GuzzleHttp\json_decode($body , true);
                }

                $this->flashHelper->addError($body);
            }
        }
    }
}