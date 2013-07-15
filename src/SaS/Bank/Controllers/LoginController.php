<?php


namespace SaS\Bank\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
/**
 * Description of LoginController
 *
 * @author drak3
 */
class LoginController extends \SaS\Controller\AbstractController {
    
    protected $route = "/login";
    protected $method = "POST|GET";
    
    
    public function getRequestConstraints() {
        return [
            'buergerID' => [
                'required' => [1000, 'buergerID wird benötigt'],
                'int' => [1001 , 'buergerID muss int sein']
            ],
            'buergerPasswort' => [
                'required' => [1002,'buergerPasswort wird benötigt']
            ]
        ];
    }
    
    public function action(\Silex\Application $app, \Symfony\Component\HttpFoundation\Request $r) {
        $id = $this->getData()['buergerID'];
        $pass = $this->getData()['buergerPasswort'];
        
        $c = $app['db.connection'];
        
        /*$sql = "SELECT * FROM users WHERE id=?";
        $stmt = $c->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->execute();*/
        
        $user = $c->fetchAssoc("SELECT * FROM users WHERE id=?", array($id));
        
        if($user === false) {
            return $this->failureResponse('Unbekannte BürgerID oder falsches Passwort');
        }
        if(!password_verify($pass, $user['pass'])) {
            return $this->failureResponse('Unbekannte BürgerID oder falsches Passwort');
        }
        $accounts = [];
        
        $privateAccount = $c->fetchAssoc('SELECT * FROM accounts WHERE ownerID = ? AND private=true', array($id));
        //$privateAccount = $statement->fetchAssoc();
        
        if($privateAccount !== false) {
            $accounts[] = [
                'kNR' => $privateAccount['kontoNR'],
                'name' => $privateAccount['name']
            ];
        }
        
        if($user['isChief']) {
            $companyAcc = $c->fetchAssoc('SELECT * FROM accounts WHERE ownerID = ? AND private=false', array($user['chiefOf_ID']));
            //$companyAcc = $statement->fetchAssoc();
            if($companyAcc !== false) {
                $accounts[] = [
                    'kNR' => $companyAcc['kontoNR'],
                    'name' => $companyAcc['name']
                ];
            }
        }
        
        return $this->successResponse($user['roles'], $user['firstName']." ".$user['lastName'], $accounts);


    }
    
    public function successResponse($rolle, $name, array $konten) {
        return new JsonResponse([
           'rolle' => $rolle,
           'name' => $name,
           'konten' => $konten,
            'fehler' => false,
            'meldung' => ''
        ]);
    }
    
    public function failureResponse($meldung) {
        return new JsonResponse([
            'rolle' => '',
            'name' => '',
            'konten' => [],
            'fehler' => true,
            'meldung' => $meldung
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
