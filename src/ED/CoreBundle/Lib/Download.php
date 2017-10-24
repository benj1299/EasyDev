<?php

namespace ED\CoreBundle\Lib;

use Symfony\Component\HttpFoundation\Response;

class Download
{
    /**
     * Télécharge un fichier
     * @param $filename
     * @param $name
     * @return Response
     */
    public function execute(string $filename, string $name){
        $response = new Response();

        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', mime_content_type($filename));
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$name.'.zip";');
        $response->headers->set('Content-length', filesize($filename));

        $response->sendHeaders(); //Ne marche pas sur chrome à tester

        $response->setContent(file_get_contents($filename));
        return $response;
    }
}
