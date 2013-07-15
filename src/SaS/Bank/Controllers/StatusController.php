<?php


namespace SaS\Bank\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
/**
 * Description of LoginController
 *
 * @author drak3
 */
class StatusController extends \SaS\Controller\AbstractController {
    
    protected $route = "/konto/status";
    protected $method = "POST|GET";
    
    
    public function getRequestConstraints() {
        return [
            'buergerID' => [
                'required' => [1000, 'buergerID wird benötigt'],
                'int' => [1001 , 'buergerID muss int sein']
            ],
            'buergerPasswort' => [
                'required' => [1002,'buergerPasswort wird benötigt']
            ],
            'baID' => [
                'required' => [1000, 'baID wird benötigt'],
                'int' => [234, 'baID muss int sein']
            ],
            'kNr' => [
                'required' => [234, 'quellKNr wird benötigt'],
            ],
            'finanzStatusVon' => [
                
            ],
            'finanzStatusBis' =>  [
                
            ]
        ];
    }
    
    public function action(\Silex\Application $app, \Symfony\Component\HttpFoundation\Request $r) {
        $id = $this->getData()['buergerID'];
        $pass = $this->getData()['buergerPasswort'];
        $baID = $this->getData()['baID'];
        $kontoNr = $this->getData()['kNr'];
        
        
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
        
        $betriebsName = '';
        $kNR = '';
        
        if($user['isChief'] || $user['roles'] == 'ba' || $user['roles'] == 'bva') {
            $companyAcc = $c->fetchAssoc('SELECT * FROM accounts WHERE ownerID = ? AND private=false', array($user['chiefOf_ID']));
            if($companyAcc !== false) {
                $kNR = $companyAcc['kontoNR'];
                $betriebsName = $companyAcc['name'];
            } else {
                return $this->failureResponse('Keine berechtigung');
            }
        } else {
            return $this->failureResponse('Keine berechtigung [2]');
        }
        
        if($kNR != $kontoNr) {
            return $this->failureResponse('keine berechtigung [3]');
        }
        
        $transactions = $c->fetchAll('SELECT * FROM transactions WHERE quellKNr = :knr OR zielKNr = :knr', ['knr' => $kNR]);
        
        $account = $companyAcc;
        
        $buchungen = [];
        
        foreach($transactions as $t) {
            $buchungen[] = [
                'buchungsNr' => $t['id'],
                'zeit' => $t['zeit'],
                'verwendungszweck' => $t['verwendungszweck'],
                'KNr' => $kontoNr,
                'betrag' => $t['betrag'],
                'bemerkungen' => $t['bemerkung']
            ];
        }
        
        return $this->successResponse($account['balance'], $account['kredit'], $buchungen);

    }
    
    public function successResponse($kontoStand, $kredit, $buchungen) {
        return new JsonResponse([
           'kontostand' => $kontoStand,
            'kreditsumme' => $kredit,
            'tilgungsrate' => '',
            'buchungen' => $buchungen,
            'zeit' => '',
            'fehler' => false,
            'meldung' => '',
        ]);
    }
    
    public function failureResponse($meldung) {
        return new JsonResponse([
           'kontostand' => '',
            'kreditsumme' => '',
            'tilgungsrate' => '',
            'buchungen' => '',
            'zeit' => '',
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
