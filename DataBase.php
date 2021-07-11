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
        $this->sql = "SELECT COD_U FROM `usuario` WHERE NOMBRE='" . $username . "'";
        $result = mysqli_query($this->connect, $this->sql);
        $row = mysqli_fetch_array($result);
        $this->sql = "SELECT * FROM `despensa` WHERE COD_U = '" . $row['COD_U'] . "'";
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
}


?>
