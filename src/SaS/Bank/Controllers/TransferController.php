<?php


namespace SaS\Bank\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
/**
 * Description of LoginController
 *
 * @author drak3
 */
class TransferController extends \SaS\Controller\AbstractController {
    
    protected $route = "/ueberweisung";
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
            'quellKNr' => [
                'required' => [234, 'quellKNr wird benötigt'],
            ],
            'zielKNr' => [
                'required' => [234, 'zielKNr wird benötigt'],
            ],
            'betrag' => [
                'required' => [234, 'betrag wird benötigt'],
                'int' => [234, 'betrag muss int sein']
            ],
            'verwendungszweck' => [
                'required' => [63, 'verwendungszweck wird benötigt'],
            ],
            'bemerkungen' => [
                'required' => [234, 'bemerkungen wird benötigt']
            ]
        ];
    }
    
    public function action(\Silex\Application $app, \Symfony\Component\HttpFoundation\Request $r) {
        $id = $this->getData()['buergerID'];
        $pass = $this->getData()['buergerPasswort'];
        $quellKNr = $this->getData()['quellKNr'];
        $zielKNr = $this->getData()['zielKNr'];
        $betrag = $this->getData()['betrag'];
        $verwendungszweck = $this->getData()['verwendungszweck'];
        $bemerkung = $this->getData()['bemerkungen'];
        $baID = $this->getData()['baID'];
        
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
        if((isset($accounts[0]['kNR']) && $accounts[0]['kNR']) == $quellKNr || (isset($accounts[1]['kNR']) && $accounts[1]['kNR'] = $quellKNr)) {
            $quell = $c->fetchAssoc('SELECT * FROM accounts WHERE kontoNR = ?', array($quellKNr));
            if($quell == false) {
                return $this->failureResponse('ungültige quellkontonummer');
            }
            $ziel = $c->fetchAssoc('SELECT * FROM accounts WHERE kontoNR = ?', array($zielKNr));
            if($ziel == false) {
                return $this->failureResponse('ungültige zielkontonummer');
            }
            $newQuellAmount = (int) $quell['balance'] - (int) $betrag;
            $newZielAmount = (int) $ziel['balance'] + (int) $betrag;
            

            if($newQuellAmount < -(int)$quell['kredit'] || $quell['isLimitless']) {
                return $this->failureResponse('Kontostand zu niedrig!');
            }
            
            //$c->beginTransaction();
            //try{
                $time = date('c');
                
                $transaction = [
                    'zeit' => $time,
                    'verwendungszweck' => $verwendungszweck,
                    'zielKNr' => $zielKNr,
                    'zielName' => $ziel['name'],
                    'quellKNr' => $quellKNr,
                    'quellName' => $quell['name'],
                    'betrag' => $betrag,
                    'bemerkung' => $bemerkung,
                    'baID' => $baID,
                    'printed' => false
                ];
                                                
                if(!$c->insert('transactions', $transaction)) {
                    return $this->failureResponse('Konnte transaktion nicht ausführen, interner Fehler [1]');
                }
                
                $transactionID = $c->lastInsertID();
            
                if(!$c->update('accounts', array('balance' => $newQuellAmount), array('id' => $quell['id']))) {
                    return $this->failureResponse('Konnte transaktion nicht ausführen, interner Fehler [2]');
                }
                if(!$c->update('accounts', array('balance' => $newZielAmount), array('id' => $ziel['id']))) {
                    return $this->failureResponse('Konnte transaktion nicht ausführen, interner Fehler [3]');
                }
                //$conn->commit();
            //} catch(Exception $e) {
            //    $conn->rollback();
            //    throw $e;
            //}
                
                        
        } else {
            return $this->failureResponse('User ist nicht berechtigt von diesem konto zu überweisen');
        }
        
        $signedTransaction = $transaction;
                unset($signedTransaction['printed']);
                $signature = $app['security.signer']->sign($signedTransaction);
        
        
        return $this->successResponse($transactionID, $time, $signature);

    }
    
    public function successResponse($buchungsNr, $zeit, $signatur) {
        return new JsonResponse([
           'buergerID' => $this->getData()['buergerID'],
           'baID' => $this->getData()['baID'],
           'quellKNr' => $this->getData()['quellKNr'],
           'zielKNr' => $this->getData()['zielKNr'],
           'betrag' => $this->getData()['betrag'],
           'verwendungszweck' => $this->getData()['verwendungszweck'],
           'bemerkungen' => $this->getData()['bemerkungen'],
           'buchungsNR' => $buchungsNr,
           'zeit' => $zeit,
            'signatur' => $signatur,
            'fehler' => false,
            'meldung' => '',
        ]);
    }
    
    public function failureResponse($meldung) {
        return new JsonResponse([
           'buergerID' => '',
           'baID' => '',
           'quellKNr' => '',
           'zielKNr' => '',
           'betrag' => '',
           'verwendungszweck' => '',
           'bemerkungen' => '',
           'buchungsNR' => '',
           'zeit' => '',
            'signatur' => '',
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
