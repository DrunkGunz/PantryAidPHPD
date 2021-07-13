<?php
require "DataBaseConfig.php";
class DataBase
{
    public $connect;
    public $data;
    private $sql;
    protected $servername;
    protected $username;
    protected $password;
    protected $databasename;
    protected $return_arr;
    public function __construct()
    {
        $this->connect = null;
        $this->data = null;
        $this->sql = null;
        $dbc = new DataBaseConfig();
        $this->servername = $dbc->servername;
        $this->username = $dbc->username;
        $this->password = $dbc->password;
        $this->databasename = $dbc->databasename;
    }

    function dbConnect()
    {
        $this->connect = mysqli_connect($this->servername, $this->username, $this->password, $this->databasename);
        return $this->connect;
    }

    function prepareData($data)
    {
        return mysqli_real_escape_string($this->connect, stripslashes(htmlspecialchars($data)));
    }

    function logIn($table, $username, $password)
    {
        $username = $this->prepareData($username);
        $password = $this->prepareData($password);
        $this->sql = "select * from " . $table . " where NOMBRE = '" . $username . "'";
        $result = mysqli_query($this->connect, $this->sql);
        $row = mysqli_fetch_assoc($result);
        if (mysqli_num_rows($result) != 0) {
            $dbusername = $row['NOMBRE'];
            $dbpassword = $row['PASSWORD'];
            if ($dbusername == $username && password_verify($password, $dbpassword)) {
                $login = true;
            } else $login = false;
        } else $login = false;
        return $login;
    }

    function reDirect($username)
    {
        $redirect=false;
        $username = $this->prepareData($username);
        $row = $this->buscarusuario($username);
        $this->sql = "SELECT * FROM `despensa` WHERE COD_U = '" . $row . "'";
        $result = mysqli_query($this->connect, $this->sql);
        $row = mysqli_fetch_assoc($result);
        if (mysqli_num_rows($result) != 0) {
            $redirect = true;
        } else $redirect = false;
        return $redirect;
    }


    function signUp($table, $username, $email, $direccion , $password, $codCO)
    {
        $table = $this->prepareData($table);
        $username = $this->prepareData($username);
        $email = $this->prepareData($email);
        $password = $this->prepareData($password);
        $direccion = $this->prepareData($direccion);
        $password = password_hash($password, PASSWORD_DEFAULT);
        $codCO = $this->prepareData($codCO);
        /*
        $text = "INSERT INTO ". $table ."  (`NOMBRE`, `EMAIL`, `DIRECCION`, `PASSWORD`) VALUES (`" . $username . "`,`" . $email . "`,`" . $direccion . "`,`" . $password . "`)";
        $var_str = var_export($text, true);
        $var = "<?php\n\n\$query = $var_str;\n\n?>";
        file_put_contents('filename.php', $var);*/

        $this->sql =
            "INSERT INTO ". $table ."  (`NOMBRE`, `EMAIL`, `DIRECCION`, `PASSWORD`, `COD_CO` )
            VALUES ('" . $username . "','" . $email . "','" . $direccion . "','" . $password . "'," . $codCO . ")";
        if (mysqli_query($this->connect, $this->sql)) {
            return true;
        } else return false;
    }

    function obtenerRegion(){
        $sql = "SELECT * FROM region";
        mysqli_set_charset($this->connect, 'utf8');

        if (!$this->connect->query($sql)) {
            echo "error conectando a la base de datos";
        }else {
            $result = $this->connect->query($sql);
            if ($result->num_rows >0) {
                $return_arr['region'] = array();
                while ($row = $result->fetch_array()) {
                    array_push($return_arr['region'],array(
                        'COD_RE'=>$row['COD_RE'],
                        'NOMBRE'=>$row['NOMBRE']
                    ));
                }
                return json_encode($return_arr);
            }
        }
    }

    function obtenerProvi($region){
        $region = $this->prepareData($region);
        $sql =
        "select provincia.COD_PRO, provincia.NOMBRE from provincia where COD_RE=(SELECT COD_RE FROM region WHERE NOMBRE='".$region."')";
                                                                                //se puede acortar desde el android studio
        mysqli_set_charset($this->connect, 'utf8');
        if (!$this->connect->query($sql)) {
            echo "error conectando a la base de datos";
        }else {
            $result = $this->connect->query($sql);
            if ($result->num_rows >0) {
                $return_arr['provincia'] = array();
                while ($row = $result->fetch_array()) {
                    array_push($return_arr['provincia'],array(
                        'COD_PRO'=>$row['COD_PRO'],
                        'NOMBRE'=>$row['NOMBRE']
                    ));
                }
                return json_encode($return_arr);
            }
        }
    }


    function obtenerDesp($username){
        $username = $this->prepareData($username);
        $codU = $this->buscarusuario($username);

        $sql ="SELECT despensa.COD_DES,
                    ingrediente.NOMBRE,
                    despensa.CANTIDAD,
                    despensa.STOCK_MINIMO,
                    envase_temporal.GRAMO
                    FROM despensa
                    JOIN tipo_envase on tipo_envase.COD_TIEN = despensa.COD_TIEN
                    JOIN ingrediente on ingrediente.COD_IN = tipo_envase.COD_IN
                    JOIN envase_temporal on envase_temporal.COD_DES = despensa.COD_DES
                    where COD_U ='".$codU."'";
            //se puede acortar desde el android studio
        mysqli_set_charset($this->connect, 'utf8');
        if (!$this->connect->query($sql)) {
            echo "error conectando a la base de datos";
        }else {
            $result = $this->connect->query($sql);
            if ($result->num_rows >0) {
                $return_arr['despensa'] = array();
                while ($row = $result->fetch_array()) {
                    array_push($return_arr['despensa'],array(
                        'COD_DES'=>$row['COD_DES'],
                        'NOMBRE'=>$row['NOMBRE'],
                        'CANTIDAD'=>$row['CANTIDAD'],
                        'STOCK_MINIMO'=>$row['STOCK_MINIMO'],
                        'GRAMO'=>$row['GRAMO']
                    ));
                }
                return json_encode($return_arr);
            }else echo "nodesp";
        }
    }


    function obtenerRecet($username){
        $username = $this->prepareData($username);
        $codU = $this->buscarusuario($username);

        $sql ="SELECT receta.COD_REC,
                    receta.NOMBRE,
                    usuario.NOMBRE AS AUTOR
                    FROM receta
                    JOIN usuario on usuario.COD_U = receta.COD_U
                    where receta.COD_U ='".$codU."'";
            //se puede acortar desde el android studio
        mysqli_set_charset($this->connect, 'utf8');
        if (!$this->connect->query($sql)) {
            echo "error conectando a la base de datos";
        }else {
            $result = $this->connect->query($sql);
            if ($result->num_rows >0) {
                $return_arr['receta'] = array();
                while ($row = $result->fetch_array()) {
                    array_push($return_arr['receta'],array(
                        'COD_REC'=>$row['COD_REC'],
                        'NOMBRE'=>$row['NOMBRE'],
                        'AUTOR'=>$row['AUTOR']
                    ));
                }
                return json_encode($return_arr);
            }else echo "nodesp";
        }
    }



    function obtenerComuna($provincia){
        $provincia = $this->prepareData($provincia);
        $sql =
        "select comuna.COD_CO, comuna.NOMBRE from comuna
        where COD_PRO=(SELECT COD_PRO FROM provincia WHERE NOMBRE='".$provincia."')";
        mysqli_set_charset($this->connect, 'utf8');
        if (!$this->connect->query($sql)) {
            echo "error conectando a la base de datos";
        }else {
            $result = $this->connect->query($sql);
            if ($result->num_rows >0) {
                $return_arr['comuna'] = array();
                while ($row = $result->fetch_array()) {
                    array_push($return_arr['comuna'],array(
                        'COD_CO'=>$row['COD_CO'],
                        'NOMBRE'=>$row['NOMBRE']
                    ));
                }
                return json_encode($return_arr);
            }
        }
    }

    function obtenerDif(){
        $sql = "SELECT * FROM dificultad";
        mysqli_set_charset($this->connect, 'utf8');

        if (!$this->connect->query($sql)) {
            echo "error conectando a la base de datos";
        }else {
            $result = $this->connect->query($sql);
            if ($result->num_rows >0) {
                $return_arr['dificultad'] = array();
                while ($row = $result->fetch_array()) {
                    array_push($return_arr['dificultad'],array(
                        'COD_DF'=>$row['COD_DF'],
                        'NOMBRE'=>$row['NOMBRE']
                    ));
                }
                return json_encode($return_arr);
            }
        }
    }



    function buscarusuario($username) {
        $username = $this->prepareData($username);
        $this->sql = "SELECT COD_U FROM `usuario` WHERE NOMBRE='" . $username . "'";
        $result = mysqli_query($this->connect, $this->sql);
        $row = mysqli_fetch_array($result);
        return $row['COD_U'];
    }

    function obtenerIngredientes(){
        $sql = "SELECT * FROM ingrediente";
        mysqli_set_charset($this->connect, 'utf8');
        if (!$this->connect->query($sql)) {
            echo "error conectando a la base de datos";
        }else {
            $result = $this->connect->query($sql);
            if ($result->num_rows >0) {
                $return_arr['ingrediente'] = array();
                while ($row = $result->fetch_array()) {
                    array_push($return_arr['ingrediente'],array(
                        'COD_IN'=>$row['COD_IN'],
                        'NOMBRE'=>$row['NOMBRE']
                    ));
                }
                return json_encode($return_arr);
            }
        }
    }

    function buscarIngrediente($ingrediente) {
        $ingrediente = $this->prepareData($ingrediente);
        $this->sql = "SELECT COD_IN FROM `ingrediente` WHERE NOMBRE='" . $ingrediente . "'";
        $result = mysqli_query($this->connect, $this->sql);
        $row = mysqli_fetch_array($result);
        return $row['COD_IN'];
    }

    function obtenerEnvase($ingrediente){
        $ingrediente = $this->prepareData($ingrediente);
        $codIN = $this->buscarIngrediente($ingrediente);

        $sql =" SELECT tipo_envase.COD_TIEN,
        envase.NOMBRE
        FROM tipo_envase
        JOIN envase ON envase.COD_EN = tipo_envase.COD_EN
        where tipo_envase.COD_IN='".$codIN."'";

        mysqli_set_charset($this->connect, 'utf8');
        if (!$this->connect->query($sql)) {
            echo "error conectando a la base de datos";
        }else {
            $result = $this->connect->query($sql);
            if ($result->num_rows >0) {
                $return_arr['envase'] = array();
                while ($row = $result->fetch_array()) {
                    array_push($return_arr['envase'],array(
                        'COD_TIEN'=>$row['COD_TIEN'],
                        'NOMBRE'=>$row['NOMBRE']
                    ));
                }
                return json_encode($return_arr);
            }
        }
    }

    function addDesp($table, $username, $cantidad, $stockmin , $cod_tien)
    {
        $table = $this->prepareData($table);
        $username = $this->prepareData($username);
        $codU = $this->buscarusuario($username);
        $cantidad = $this->prepareData($cantidad);
        $cod_tien = $this->prepareData($cod_tien);
        $stockmin = $this->prepareData($stockmin);
        /*
        $text = "INSERT INTO ". $table ."  (`NOMBRE`, `EMAIL`, `DIRECCION`, `PASSWORD`) VALUES (`" . $username . "`,`" . $email . "`,`" . $direccion . "`,`" . $password . "`)";
        $var_str = var_export($text, true);
        $var = "<?php\n\n\$query = $var_str;\n\n?>";
        file_put_contents('filename.php', $var);*/

        $this->sql =
            "INSERT INTO ". $table ."  ( `STOCK_MINIMO`, `CANTIDAD`, `COD_U`, `COD_TIEN` )
            VALUES ('" . $stockmin . "','" . $cantidad . "','" . $codU . "','" . $cod_tien . "')";
        if (mysqli_query($this->connect, $this->sql)) {
            $this->addTen($cod_tien, $codU);
            return true;
        } else return false;
    }

    function addRec($table, $username, $nombre, $descripcion , $prep, $codDF)
    {
        $table = $this->prepareData($table);
        $username = $this->prepareData($username);
        $codU = $this->buscarusuario($username);
        $nombre = $this->prepareData($nombre);
        $prep = $this->prepareData($prep);
        $descripcion = $this->prepareData($descripcion);
        $codDF = $this->prepareData($codDF);

        /*
        $text = "INSERT INTO ". $table ."  (`NOMBRE`, `EMAIL`, `DIRECCION`, `PASSWORD`) VALUES (`" . $username . "`,`" . $email . "`,`" . $direccion . "`,`" . $password . "`)";
        $var_str = var_export($text, true);
        $var = "<?php\n\n\$query = $var_str;\n\n?>";
        file_put_contents('filename.php', $var);*/

        $this->sql =
            "INSERT INTO ". $table ."  ( `NOMBRE`, `DESCRIPCION`, `PREPARACION`, `COD_U`, `COD_DF` )
            VALUES ('" . $nombre . "','" . $descripcion . "','" . $prep . "','" . $codU . "','" . $codDF . "')";
        if (mysqli_query($this->connect, $this->sql)) {
            return true;
        } else return false;
    }

    function addTen($cod_tien, $codU){
        $cod_tien = $this->prepareData($cod_tien);
        $codU = $this->prepareData($codU);
        $this->sql = "SELECT despensa.COD_DES,
                            envase.GRAMO
                            FROM despensa
                            JOIN tipo_envase on tipo_envase.COD_TIEN = despensa.COD_TIEN
                            join envase on envase.COD_EN = tipo_envase.COD_EN
                            WHERE despensa.COD_U ='" . $codU . "' && despensa.COD_TIEN='".$cod_tien."'";
        $result = mysqli_query($this->connect, $this->sql);
        $row = mysqli_fetch_array($result);
        $codDes = $row['COD_DES'];
        $gramo = $row['GRAMO'];

        $this->sql ="INSERT INTO `envase_temporal` ( `GRAMO`, `COD_DES`)
                        VALUES ('".$gramo."', '".$codDes."')";
        if (mysqli_query($this->connect, $this->sql)) {

            return true;
        } else return false;
    }


    function addEnvase($table, $codIN, $nombGra, $gramo)
    {
        $table = $this->prepareData($table);
        $codIN = $this->prepareData($codIN);
        $nombGra = $this->prepareData($nombGra);
        $gramo = $this->prepareData($gramo);
        /*
        $text = "INSERT INTO ". $table ."  (`NOMBRE`, `EMAIL`, `DIRECCION`, `PASSWORD`) VALUES (`" . $username . "`,`" . $email . "`,`" . $direccion . "`,`" . $password . "`)";
        $var_str = var_export($text, true);
        $var = "<?php\n\n\$query = $var_str;\n\n?>";
        file_put_contents('filename.php', $var);*/

        $this->sql =
            "INSERT INTO ". $table ."  ( `NOMBRE`, `GRAMO` )
            VALUES ('" . $nombGra . "','" . $gramo . "')";
        if (mysqli_query($this->connect, $this->sql)) {
            $this->addtien($nombGra, $gramo, $codIN);
            return true;
        } else return false;
    }


    function addtien($nombGra, $gramo, $codIN){

        $codIN = $this->prepareData($codIN);
        $nombGra = $this->prepareData($nombGra);
        $gramo = $this->prepareData($gramo);
        $this->sql = "SELECT envase.COD_EN
                        FROM envase
                        WHERE NOMBRE='" . $nombGra . "' && GRAMO='".$gramo."'";
        $result = mysqli_query($this->connect, $this->sql);
        $row = mysqli_fetch_array($result);
        $coden = $row['COD_EN'];

        $this->sql =    "INSERT INTO `tipo_envase` ( `COD_EN`, `COD_IN`)
                            VALUES ( '".$coden."', '".$codIN."')";
        
        if (mysqli_query($this->connect, $this->sql)) {
            return true;
        } else return false;
    }

    function addTientito($codIN, $coden){

        $codIN = $this->prepareData($codIN);
        $coden = $this->prepareData($coden);

        $this->sql ="INSERT INTO `tipo_envase` ( `COD_EN`, `COD_IN`)
                        VALUES ( '".$coden."', '".$codIN."')";
        
        if (mysqli_query($this->connect, $this->sql)) {
        
            return true;
        } else return false;
    }




    function obtenerEnvasesolo(){

        $sql =" SELECT * FROM envase";

        mysqli_set_charset($this->connect, 'utf8');
        if (!$this->connect->query($sql)) {
            echo "error conectando a la base de datos";
        }else {
            $result = $this->connect->query($sql);
            if ($result->num_rows >0) {
                $return_arr['envase'] = array();
                while ($row = $result->fetch_array()) {
                    array_push($return_arr['envase'],array(
                        'COD_EN'=>$row['COD_EN'],
                        'NOMBRE'=>$row['NOMBRE']
                    ));
                }
                return json_encode($return_arr);
            }
        }
    }





}


?>
