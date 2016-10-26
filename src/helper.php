<?php

function publish($filePage, $data = [])
{
    $hermeticRender = function ($____f, $____d) {
        extract($____d);
        unset($____d);
        ob_start();
        include $____f;
        return ob_get_clean();
    };
    $filePage = concatPath('./page/', $filePage);
    Vue::setContent($hermeticRender($filePage, $data));
    ob_start();
    include 'template.php';
    $body = ob_get_contents();
    ob_end_clean();
    //remove redundant (white-space) characters
    $replace = array(
        //remove tabs before and after HTML tags
        '/\>[^\S ]+/s' => '>',
        '/[^\S ]+\</s' => '<',
        //shorten multiple whitespace sequences; keep new-line characters because they matter in JS!!!
        '/([\t ])+/s' => ' ',
        //remove leading and trailing spaces
        '/^([\t ])+/m' => '',
        '/([\t ])+$/m' => '',
        // remove JS line comments (simple only); do NOT remove lines containing URL (e.g. 'src="http://server.com/"')!!!
        '~//[a-zA-Z0-9 ]+$~m' => '',
        //remove empty lines (sequence of line-end and white-space characters)
        '/[\r\n]+([\t ]?[\r\n]+)+/s' => "\n",
        //remove empty lines (between HTML tags); cannot remove just any line-end characters because in inline JS they can matter!
        '/\>[\r\n\t ]+\</s' => ">\n<",
        //remove "empty" lines containing only JS's block end character; join with next line (e.g. "}\n}\n</script>" --> "}}</script>"
        '/}[\r\n\t ]+/s' => '}',
        '/}[\r\n\t ]+,[\r\n\t ]+/s' => '},',
        //remove new-line after JS's function or condition start; join with next line
        '/\)[\r\n\t ]?{[\r\n\t ]+/s' => '){',
        '/,[\r\n\t ]?{[\r\n\t ]+/s' => ',{',
        //remove new-line after JS's line end (only most obvious and safe cases)
        '/\),[\r\n\t ]+/s' => '),',
        //remove quotes from HTML attributes that does not contain spaces; keep quotes around URLs!
        '~([\r\n\t ])?([a-zA-Z0-9]+)="([a-zA-Z0-9_/\\-]+)"([\r\n\t ])?~s' => '$1$2=$3$4', //$1 and $4 insert first white-space character found before/after attribute
    );
    //$body = preg_replace(array_keys($replace), array_values($replace), $body);
    echo $body;
}

function concatPath($debut, $fin, $separator = '/')
{
    $path = preg_replace("/[\\/\\\\]+(?:.[\\/\\\\]+)*/", $separator, $debut . $separator . $fin);
    return $path;
}

function startWith($string, $search, $iCase = true)
{
    if ($iCase) {
        return strtoupper(substr($string, strlen($search))) == strtoupper($search);
    } else {
        return (substr($string, strlen($search))) == ($search);
    }
}

function url($nom_url, array $options = [], array $attrib = [])
{
    return \Route::urlFor($nom_url, $options, $attrib);
}

function asset($partial_url)
{
    $url = concatPath(\Route::getBaseUrl(), $partial_url);
    return $url;
}

function get($arg, $defaut = null)
{
    return (isset($_GET[$arg]) ? $_GET[$arg] : $defaut);
}

function post($arg, $defaut = null)
{
    return (isset($_POST[$arg]) ? $_POST[$arg] : $defaut);
}

function session($arg, $defaut = null)
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return (isset($_SESSION[$arg]) ? $_SESSION[$arg] : $defaut);
    } else {
        return $defaut;
    }
}

function getPage()
{
    return get('p', 'index');
}

function getUrl()
{
    $requestUri = explode('?', $_SERVER['REQUEST_URI']);
    $baseScript = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $url = substr($requestUri[0], strlen($baseScript));
    return $url;
}

function afficheErreurException(Exception $e)
{
    http_response_code(500);
    echo "<div class = \"error\">";
    echo ($e->getMessage());
    echo "</div>";
}

function afficheErreurMessage($message)
{
    http_response_code(500);
    echo "<div class = \"error\">";
    echo nl2br($message);
    echo "</div>";
}

function fetchall2table(array $fetchall, array $fields)
{
    $get = function ($data, $property) {
        if (is_array($data) && isset($data[$property])) {
            return $data[$property];
        } elseif (is_object($data) && isset($data->$property)) {
            return $data->$property;
        }
        return null;
    };
    $contents = "<table><tbody><tr>";
    foreach ($fields as $field) {
        if (is_string($field)) {
            $libelle = $field;
        } elseif (is_array($field)) {
            $libelle = $field[1];
        }
        if (startWith($libelle, '!_')) {
            $content .= "<th>" . substr($libelle, 2) . "</th>";
        } else {
            $content .= "<th>" . htmlentities($libelle, ENT_COMPAT | ENT_HTML401, 'cp1252') . "</th>";
        }
    }
    $content .= "</tr>";
    foreach ($fetchall as $row) {
        $content .= "<tr>";
        foreach ($fields as $field) {
            $content .= "<td>";
            if (is_string($field)) {
                $libelle = $get($row, $field);
            } elseif (is_array($field)) {
                $libelle = $get($row, $field[0]);
            }
            $content .= htmlentities($libelle, ENT_COMPAT | ENT_HTML401, 'cp1252');
            $content .= "</td>";
        }
        $content .= "</tr>";
    }
    $content .= "</tbody></table>";
    return $contents;
}

function historiz()
{
    if (!isset($_SESSION['histo'])) {
        $_SESSION['histo'] = [];
    }
    array_push($_SESSION['histo'], $_SERVER['REQUEST_URI']);
}

function urlGoBack()
{
    if (isset($_SESSION['histo']) && count($_SESSION['histo']) > 0) {
        array_pop($_SESSION['histo']);
        return array_pop($_SESSION['histo']);
    } else {
        return '/index.php';
    }
}

function dd($variable)
{
    echo PHP_EOL;
    echo '<pre>';
    var_dump($variable);
    echo '</pre>';
    exit(0);
}

function html($message, $charset = 'cp1252')
{
    return htmlentities($message, ENT_COMPAT | ENT_HTML401, 'cp1252');
}

function redirect($num, $url)
{
    http_response_code($num);
    if (!isAjaxMethode()) {
        header("Location: $url");
    }
    exit(1);
}

function isPostMethode()
{
    if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
        return true;
    }
    return false;
}

function isAjaxMethode()
{
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
        return true;
    }
    return false;
}

function MyJsonEncode($data, $option = [])
{

    $option = array_merge(
        [
        "STRING2HTML" => false
        ],
        $option
    );

    $is_assoc = function ($array) {
        foreach (array_keys($array) as $k => $v) {
            if ($k !== $v) {
                return true;
            }
        }
        return false;
    };

    switch (gettype($data)) {
        case "boolean":
            if ($data) {
                $out = ('true');
            } else {
                $out = ('false');
            }
            break;
        case "integer":
            $out = ("" . $data);
            break;
        case "double":
            $out = ("" . $data);
            break;
        case "string":
            if ($option['STRING2HTML']) {
                $out = ('"' . html($data) . '"');
            } else {
                $out = ('"' . $data . '"');
            }
            break;
        case "array":
            if ($is_assoc($data)) {
                $out = '{';
                $start = true;
                foreach ($data as $key => $value) {
                    if ($start) {
                        $start = false;
                    } else {
                        $out .= ',';
                    }
                    $out .= MyJsonEncode($key, ["STRING2HTML" => false]) . ':';
                    $out .= MyJsonEncode($value, $option);
                }
                $out .= '}';
            } else {
                $out = '[';
                $start = true;
                foreach ($data as $value) {
                    if ($start) {
                        $start = false;
                    } else {
                        $out .= ',';
                    }
                    $out .= MyJsonEncode($value, $option);
                }
                $out .= ']';
            }
            break;
        case "object":
            $obj = new ReflectionObject($data);
            $properties = $obj->getProperty(ReflectionProperty::IS_PUBLIC);
            $out = '{';
            $start = true;
            foreach ($properties as $property) {
                if ($start) {
                    $start = false;
                } else {
                    $out .= ',';
                }
                $out .= MyJsonEncode($property->name, ["STRING2HTML" => false]) . ':';
                $out .= MyJsonEncode($property->getValue($data), $option);
            }
            $out .= '}';
            break;
        case "NULL":
            $out = ('null');
            break;
        default:
            throw new RuntimeException("La donnée passé en paramètre de la fonction MyJsonEncode n'est pas valide");
    }
    return $out;
}

function hoock($string)
{
    $handle = fopen(__DIR__ . '/log.txt', 'a+');
    fwrite($handle, $string . "\r\n");
    fclose($handle);
    return $string;
}

function bitConverter($val, $unit = '')
{
    $units = [
        'o', 'Ko', 'Mo', 'Go', 'To', 'Po'
    ];
    $index = array_search(strtoupper($unit), array_map('strtoupper', $units));
    if ($index !== false) {
        return '' . round($val / pow(1024, $index), 3) . ' ' . $units[$index];
    }
    $index = 0;
    while ($val > 1024 && $index < count($units)) {
        $val = $val / 1024;
        $index++;
    }
    if ($index >= count($units)) {
        $index = count($units) - 1;
    }
    return '' . round($val, 3) . ' ' . $units[$index];
}

function pathToTemplate($templateName, $fromPage = false)
{
    $templateFile = 'template/' . str_replace('.', '/', $templateName) . '.phtml';
    if ($fromPage) {
        $templateFile = '../' . $templateFile;
    }
    return $templateFile;
}

function trace($message)
{
    $handle = fopen('./log.txt', 'a+');
    fwrite($handle, '[' . date('r') . '] > ' . $message . PHP_EOL);
    fclose($handle);
}

/**
 * Extrait un simple tableau d'un tableau d'objet.
 *
 * @param array $list_object tableau d'objet
 * @param string $key propriété qui servira de clé
 * @param string $value propriété qui servira de valeur
 * @return array
 */
function extractFromObject(array $list_object, $key, $value)
{
    $out = [];
    foreach ($list_object as $object) {
        $out[$object->{$key}] = $object->{$value};
    }
    return $out;
}
