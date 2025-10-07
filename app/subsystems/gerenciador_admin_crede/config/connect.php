<?php
class connect
{
    protected $connect;
    protected $connect_estgdm;
    protected $connect_epaf;
    protected $connect_epmfm;
    protected $connect_epav;
    protected $connect_eedq;
    protected $connect_ejin;
    protected $connect_epfads;
    protected $connect_emcvm;
    protected $connect_eglgfm;
    protected $connect_epldtv;
    protected $connect_ercr;

    function __construct()
    {
        $this->connect_database();
    }

    function connect_database()
    {
        try {
            $config = require(__DIR__ . "/../../../.env/config.php");

            // Tentar primeiro o banco local
            try {
                $host = $config['local']['crede_users']['host'];
                $database = $config['local']['crede_users']['banco'];
                $user = $config['local']['crede_users']['user'];
                $password = $config['local']['crede_users']['senha'];
                $this->connect = new PDO('mysql:host=' . $host . ';dbname=' . $database . ';charset=utf8', $user, $password);
                $this->connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connect->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


                $host_estgdm = $config['local']['ss_estgdm']['host'];
                $database_estgdm = $config['local']['ss_estgdm']['banco'];
                $user_estgdm = $config['local']['ss_estgdm']['user'];
                $password_estgdm = $config['local']['ss_estgdm']['senha'];
                $this->connect_estgdm = new PDO('mysql:host=' . $host_estgdm . ';dbname=' . $database_estgdm . ';charset=utf8', $user_estgdm, $password_estgdm);
                $this->connect_estgdm->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connect_estgdm->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


                $host_epaf = $config['local']['ss_epaf']['host'];
                $database_epaf = $config['local']['ss_epaf']['banco'];
                $user_epaf = $config['local']['ss_epaf']['user'];
                $password_epaf = $config['local']['ss_epaf']['senha'];
                $this->connect_epaf = new PDO('mysql:host=' . $host_epaf . ';dbname=' . $database_epaf . ';charset=utf8', $user_epaf, $password_epaf);
                $this->connect_epaf->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connect_epaf->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                $host_epmfm = $config['local']['ss_epmfm']['host'];
                $database_epmfm = $config['local']['ss_epmfm']['banco'];
                $user_epmfm = $config['local']['ss_epmfm']['user'];
                $password_epmfm = $config['local']['ss_epmfm']['senha'];
                $this->connect_epmfm = new PDO('mysql:host=' . $host_epmfm . ';dbname=' . $database_epmfm . ';charset=utf8', $user_epmfm, $password_epmfm);
                $this->connect_epmfm->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connect_epmfm->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


                $host_epav = $config['local']['ss_epav']['host'];
                $database_epav = $config['local']['ss_epav']['banco'];
                $user_epav = $config['local']['ss_epav']['user'];
                $password_epav = $config['local']['ss_epav']['senha'];
                $this->connect_epav = new PDO('mysql:host=' . $host_epav . ';dbname=' . $database_epav . ';charset=utf8', $user_epav, $password_epav);
                $this->connect_epav->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connect_epav->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


                $host_eedq = $config['local']['ss_eedq']['host'];
                $database_eedq = $config['local']['ss_eedq']['banco'];
                $user_eedq = $config['local']['ss_eedq']['user'];
                $password_eedq = $config['local']['ss_eedq']['senha'];
                $this->connect_eedq = new PDO('mysql:host=' . $host_eedq . ';dbname=' . $database_eedq . ';charset=utf8', $user_eedq, $password_eedq);
                $this->connect_eedq->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connect_eedq->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


                $host_ejin = $config['local']['ss_ejin']['host'];
                $database_ejin = $config['local']['ss_ejin']['banco'];
                $user_ejin = $config['local']['ss_ejin']['user'];
                $password_ejin = $config['local']['ss_ejin']['senha'];
                $this->connect_ejin = new PDO('mysql:host=' . $host_ejin . ';dbname=' . $database_ejin . ';charset=utf8', $user_ejin, $password_ejin);
                $this->connect_ejin->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connect_ejin->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


                $host_epfads = $config['local']['ss_epfads']['host'];
                $database_epfads = $config['local']['ss_epfads']['banco'];
                $user_epfads = $config['local']['ss_epfads']['user'];
                $password_epfads = $config['local']['ss_epfads']['senha'];
                $this->connect_epfads = new PDO('mysql:host=' . $host_epfads . ';dbname=' . $database_epfads . ';charset=utf8', $user_epfads, $password_epfads);
                $this->connect_epfads->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connect_epfads->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


                $host_emcvm = $config['local']['ss_emcvm']['host'];
                $database_emcvm = $config['local']['ss_emcvm']['banco'];
                $user_emcvm = $config['local']['ss_emcvm']['user'];
                $password_emcvm = $config['local']['ss_emcvm']['senha'];
                $this->connect_emcvm = new PDO('mysql:host=' . $host_emcvm . ';dbname=' . $database_emcvm . ';charset=utf8', $user_emcvm, $password_emcvm);
                $this->connect_emcvm->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connect_emcvm->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


                $host_eglgfm = $config['local']['ss_eglgfm']['host'];
                $database_eglgfm = $config['local']['ss_eglgfm']['banco'];
                $user_eglgfm = $config['local']['ss_eglgfm']['user'];
                $password_eglgfm = $config['local']['ss_eglgfm']['senha'];
                $this->connect_eglgfm = new PDO('mysql:host=' . $host_eglgfm . ';dbname=' . $database_eglgfm . ';charset=utf8', $user_eglgfm, $password_eglgfm);
                $this->connect_eglgfm->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connect_eglgfm->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


                $host_epldtv = $config['local']['ss_epldtv']['host'];
                $database_epldtv = $config['local']['ss_epldtv']['banco'];
                $user_epldtv = $config['local']['ss_epldtv']['user'];
                $password_epldtv = $config['local']['ss_epldtv']['senha'];
                $this->connect_epldtv = new PDO('mysql:host=' . $host_epldtv . ';dbname=' . $database_epldtv . ';charset=utf8', $user_epldtv, $password_epldtv);
                $this->connect_epldtv->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connect_epldtv->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


                $host_ercr = $config['local']['ss_ercr']['host'];
                $database_ercr = $config['local']['ss_ercr']['banco'];
                $user_ercr = $config['local']['ss_ercr']['user'];
                $password_ercr = $config['local']['ss_ercr']['senha'];
                $this->connect_ercr = new PDO('mysql:host=' . $host_ercr . ';dbname=' . $database_ercr . ';charset=utf8', $user_ercr, $password_ercr);
                $this->connect_ercr->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connect_ercr->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                // Se falhar, tentar o banco da hospedagem
                $host = $config['hospedagem']['crede_users']['host'];
                $database = $config['hospedagem']['crede_users']['banco'];
                $user = $config['hospedagem']['crede_users']['user'];
                $password = $config['hospedagem']['crede_users']['senha'];

                $this->connect = new PDO('mysql:host=' . $host . ';dbname=' . $database . ';charset=utf8', $user, $password);
                $this->connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connect->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                $host_estgdm = $config['hospedagem']['ss_estgdm']['host'];
                $database_estgdm = $config['hospedagem']['ss_estgdm']['banco'];
                $user_estgdm = $config['hospedagem']['ss_estgdm']['user'];
                $password_estgdm = $config['hospedagem']['ss_estgdm']['senha'];
                $this->connect_estgdm = new PDO('mysql:host=' . $host_estgdm . ';dbname=' . $database_estgdm . ';charset=utf8', $user_estgdm, $password_estgdm);
                $this->connect_estgdm->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connect_estgdm->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


                $host_epaf = $config['hospedagem']['ss_epaf']['host'];
                $database_epaf = $config['hospedagem']['ss_epaf']['banco'];
                $user_epaf = $config['hospedagem']['ss_epaf']['user'];
                $password_epaf = $config['hospedagem']['ss_epaf']['senha'];
                $this->connect_epaf = new PDO('mysql:host=' . $host_epaf . ';dbname=' . $database_epaf . ';charset=utf8', $user_epaf, $password_epaf);
                $this->connect_epaf->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connect_epaf->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                $host_epmfm = $config['hospedagem']['ss_epmfm']['host'];
                $database_epmfm = $config['hospedagem']['ss_epmfm']['banco'];
                $user_epmfm = $config['hospedagem']['ss_epmfm']['user'];
                $password_epmfm = $config['hospedagem']['ss_epmfm']['senha'];
                $this->connect_epmfm = new PDO('mysql:host=' . $host_epmfm . ';dbname=' . $database_epmfm . ';charset=utf8', $user_epmfm, $password_epmfm);
                $this->connect_epmfm->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connect_epmfm->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


                $host_epav = $config['hospedagem']['ss_epav']['host'];
                $database_epav = $config['hospedagem']['ss_epav']['banco'];
                $user_epav = $config['hospedagem']['ss_epav']['user'];
                $password_epav = $config['hospedagem']['ss_epav']['senha'];
                $this->connect_epav = new PDO('mysql:host=' . $host_epav . ';dbname=' . $database_epav . ';charset=utf8', $user_epav, $password_epav);
                $this->connect_epav->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connect_epav->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


                $host_eedq = $config['hospedagem']['ss_eedq']['host'];
                $database_eedq = $config['hospedagem']['ss_eedq']['banco'];
                $user_eedq = $config['hospedagem']['ss_eedq']['user'];
                $password_eedq = $config['hospedagem']['ss_eedq']['senha'];
                $this->connect_eedq = new PDO('mysql:host=' . $host_eedq . ';dbname=' . $database_eedq . ';charset=utf8', $user_eedq, $password_eedq);
                $this->connect_eedq->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connect_eedq->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


                $host_ejin = $config['hospedagem']['ss_ejin']['host'];
                $database_ejin = $config['hospedagem']['ss_ejin']['banco'];
                $user_ejin = $config['hospedagem']['ss_ejin']['user'];
                $password_ejin = $config['hospedagem']['ss_ejin']['senha'];
                $this->connect_ejin = new PDO('mysql:host=' . $host_ejin . ';dbname=' . $database_ejin . ';charset=utf8', $user_ejin, $password_ejin);
                $this->connect_ejin->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connect_ejin->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


                $host_epfads = $config['hospedagem']['ss_epfads']['host'];
                $database_epfads = $config['hospedagem']['ss_epfads']['banco'];
                $user_epfads = $config['hospedagem']['ss_epfads']['user'];
                $password_epfads = $config['hospedagem']['ss_epfads']['senha'];
                $this->connect_epfads = new PDO('mysql:host=' . $host_epfads . ';dbname=' . $database_epfads . ';charset=utf8', $user_epfads, $password_epfads);
                $this->connect_epfads->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connect_epfads->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


                $host_emcvm = $config['hospedagem']['ss_emcvm']['host'];
                $database_emcvm = $config['hospedagem']['ss_emcvm']['banco'];
                $user_emcvm = $config['hospedagem']['ss_emcvm']['user'];
                $password_emcvm = $config['hospedagem']['ss_emcvm']['senha'];
                $this->connect_emcvm = new PDO('mysql:host=' . $host_emcvm . ';dbname=' . $database_emcvm . ';charset=utf8', $user_emcvm, $password_emcvm);
                $this->connect_emcvm->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connect_emcvm->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


                $host_eglgfm = $config['hospedagem']['ss_eglgfm']['host'];
                $database_eglgfm = $config['hospedagem']['ss_eglgfm']['banco'];
                $user_eglgfm = $config['hospedagem']['ss_eglgfm']['user'];
                $password_eglgfm = $config['hospedagem']['ss_eglgfm']['senha'];
                $this->connect_eglgfm = new PDO('mysql:host=' . $host_eglgfm . ';dbname=' . $database_eglgfm . ';charset=utf8', $user_eglgfm, $password_eglgfm);
                $this->connect_eglgfm->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connect_eglgfm->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


                $host_epldtv = $config['hospedagem']['ss_epldtv']['host'];
                $database_epldtv = $config['hospedagem']['ss_epldtv']['banco'];
                $user_epldtv = $config['hospedagem']['ss_epldtv']['user'];
                $password_epldtv = $config['hospedagem']['ss_epldtv']['senha'];
                $this->connect_epldtv = new PDO('mysql:host=' . $host_epldtv . ';dbname=' . $database_epldtv . ';charset=utf8', $user_epldtv, $password_epldtv);
                $this->connect_epldtv->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connect_epldtv->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


                $host_ercr = $config['hospedagem']['ss_ercr']['host'];
                $database_ercr = $config['hospedagem']['ss_ercr']['banco'];
                $user_ercr = $config['hospedagem']['ss_ercr']['user'];
                $password_ercr = $config['hospedagem']['ss_ercr']['senha'];
                $this->connect_ercr = new PDO('mysql:host=' . $host_ercr . ';dbname=' . $database_ercr . ';charset=utf8', $user_ercr, $password_ercr);
                $this->connect_ercr->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connect_ercr->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {

            error_log("Erro de conexão com banco: " . $e->getMessage());
            $this->connect = null;
            header('location:../views/windows/desconnect.php');
            exit();
        }
    }
}
