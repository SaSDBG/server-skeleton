<?php


namespace SaS\Bank\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
/**
 * Description of LoginController
 *
 * @author drak3
 */
class KreditController extends \SaS\Controller\AbstractController {
    
    protected $route = "/kredit";
    protected $method = "POST|GET";
    
    
    public function getRequestConstraints() {
        return [
            'bvaID' => [
                'required' => [1000, 'bvaID wird benötigt'],
                'int' => [1001 , 'bvaID muss int sein']
            ],
            'bvaPasswort' => [
                'required' => [1002,'bvaPasswort wird benötigt']
            ],
            'kNr' => [
                'required' => [234, 'kNr wird benötigt'],
            ],
            'kredithoehe' => [
                'required' => [234, 'kreditHoehe wird benötigt']
            ]
       ];
    }
    
    public function action(\Silex\Application $app, \Symfony\Component\HttpFoundation\Request $r) {
        $id = $this->getData()['bvaID'];
        $pass = $this->getData()['bvaPasswort'];
        $kNR = $this->getData()['kNr'];
        $kredit = $this->getData()['kredithoehe'];
        
        
        $c = $app['db.connection'];
        
        
        
        $user = $c->fetchAssoc("SELECT * FROM users WHERE id=?", array($id));
        
        if($user === false) {
            return $this->failureResponse('Unbekannte BürgerID oder falsches Passwort');
        }
        if(!password_verify($pass, $user['pass'])) {
            return $this->failureResponse('Unbekannte BürgerID oder falsches Passwort');
        }
        if(!($user['roles'] == 'bva' || $user['roles'] == 'ba')) {
            return $this->failureResponse('Keine berechtigung');
        }
        
        if(!$c->fetchArray('SELECT id FROM accounts WHERE kontoNR=?', [$kNR])) {
            return $this->failureResponse('Unbekanntes konto');
        }
        
        $c->update('accounts', ['kredit' => $kredit], ['kontoNR' => $kNR]);
        
        return $this->successResponse();
    }
    
    public function successResponse() {
        return new JsonResponse([
            'fehler' => false,
            'meldung' => '',
        ]);
    }
    
    public function failureResponse($meldung) {
        return new JsonResponse([
            'fehler' => true,
            'meldung' => $meldung,
        ]);
    }
    

    public function getSecurityError() {
        return [];
    }

    public function getSecurityRequirements() {
        return [];
    }

    

}

?>
