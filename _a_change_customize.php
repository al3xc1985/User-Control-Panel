<?php

    include_once('_template/_header.php');

    if(!_getUsername())
        Header('Location: index.php');

    $SA         = null;
    $RealmID    = null;
    $GUID       = null;
    $REALSON    = null;

    if(isset($_GET['realmid']) && isset($_GET['guid'])) {
        unset($_SESSION['TCA']);
        $RealmID    = $_GET['realmid'];
        $GUID       = $_GET['guid'];
        if(!is_numeric($RealmID) || !is_numeric($GUID))
            Header('Location: _userside.php');
    } else if(isset($_SESSION['TCA'])) {
        $SA         = $_SESSION['TCA'];
        $RealmID    = $_SESSION['TCA']['RealmID'];
        $RealmName  = $_SESSION['TCA']['RealmName'];
        $GUID       = $_SESSION['TCA']['CharGUID'];
        $CharName   = $_SESSION['TCA']['CharName'];

        if(!_doesCharacterHaveAFlag($GUID, $RealmID, $DBUser, $DBPassword, "0x08")) {
            $connection = _MySQLConnect($AccountDBHost, $DBUser, $DBPassword, $AccountDB);
            if(_isEnoughMythCoins($PriceForCharChangeCustomize, $connection)) {
                _SpendMythCoins($PriceForCharChangeName, 3, $CharName, $GUID, $RealmName, $RealmID, "", $connection);
                mysql_close($connection) or die(mysql_error());
                $REALSON = _GDiv($L[29]);
                _addFlag_Character($GUID, $RealmID, $DBUser, $DBPassword, "0x08");
            } else {
                mysql_close($connection) or die(mysql_error());
                $REALSON = _getNotEnoughtFireSTR();
            }
        }
        unset($_SESSION['TCA']);
    } else Header('Location: _userside.php'); // die("EXEPTION");

    if(_doesRealmExists($RealmID, $DBUser, $DBPassword, isset($SA))) {
        if(_doesCharacterExistsOnAccount($DBUser, $DBPassword, $RealmID, $GUID, isset($SA))) {
            if(_doesCharacterNotOnlineATM($DBUser, $DBPassword, $RealmID, $GUID)) {
                if(_doesCharacterHaveAFlag($GUID, $RealmID, $DBUser, $DBPassword, "0x08"))
                    echo _getAlreadyEffectSTR($L[68]);
                else
                    _FORM_TO_CHAR_ACTIONS($SA ? $SA : _FORM_CHAR_ARRAY($AccountDBHost, $AccountDB, $DBUser, $DBPassword, $RealmID, $GUID) /* CHECK FOR SESSION ARRAY */,
                                        $L[84], $REALSON, $PriceForCharChangeCustomize,
                                         null /* NEW LEVEL */,
                                         true /* NEW NAME */,
                                         null /* NEW RACE */,
                                         null /* NEW CLASS */,
                                         true /* CUSTOMIZE */);
            } else echo _RDiv($L[60]);
        } else echo _RDiv($L[9]);
    } else echo _RDiv($L[9]);

    include_once('_template/_footer.php');
    ob_end_flush();
?>