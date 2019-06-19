<style>
    .code{background-color: #c0c0c0; font-weight: 800; padding: 0.80em; border: solid 1px #000; box-shadow: 5px 5px #d3d3d3;}
</style>

<?php
    //função verifica se email ou userName ja existem no bd
    function nomeUserVerify($con, $field)
    {
        $query = $con->prepare("SELECT nome_User FROM tbl_usuarios WHERE nome_User = ?;");
        $query->bind_param("s", $field);
        $query->execute();
        $get = $query->get_result();
        $verify = $get->fetch_assoc();

        if($field == $verify['nome_User'])
        {
            $get->free_result();
            return false;
        }else{
            $get->free_result();
            return true;
        }
    }

    //função verifica se email ja existe no bd
    function emailUserVerify($con, $field)
    {
        $query = $con->prepare("SELECT email_User FROM tbl_usuarios WHERE email_User = ?");
        $query->bind_param("s", $field);
        $query->execute();
        $get = $query->get_result();
        $verify = $get->fetch_assoc();

        if($field == $verify['email_User']){
            $get->free_result();
            return false;
        }else{
            $get->free_result();
            return true;
        }
    }

    //verifica se as senhas coincidem
    function passwordVerify($field1, $field2)
    {
        //field1->senha_User
        //field2->confirmarPassword
        if($field2 != $field1){
            return false;
        }else{
            return true;
        }
    }

    /* Verifica se o nome do jogo já existe na base de dados*/
    function gameNameVerify($con, $name)
    {
        $query = $con->prepare("SELECT nomeJogo_Upload FROM tbl_uploads WHERE nomeJogo_Upload = ?");
        $query->bind_param("s", $name);
        $query->execute();
        $get = $query->get_result();
        $verify = $get->fetch_assoc();

        if($name == $verify['nomeJogo_Upload']) // Se já houver um jogo com esse nome
        {
            $get->free_result();
            return false; // a função retorna falso
        }
        else
        {
            $get->free_result();
            return true; // Senao houver nenhum jogo com esse nome, a funcao retorna verdadeiro
        }
    }

    // Remove espaços excessivos e caracteres especiais
    function filterField($field)
    {
        $field = trim($field);
        $field = stripslashes($field);
        $field = htmlspecialchars($field);

        return $field;
    }

    // mostra um modal na pagina com o titulo, texto e estilo especificado
    function showModal($title, $message, $type = null)
    /* Args 
    * $title = titulo do modal
    * $message = texto de corpo do modal
    * $type = estilo do modal: 'success' para estilo postivo(verde) ou 'error' para estilo negativo(vermelho)
        ou nada para um estilo padrão
    */
    {
        switch ($type) {
            case 'success':
                $color = 'success';
                $bgcolor = '#DFFFDF';
                break;
            case 'error':
                $color = 'danger';
                $bgcolor = '#FFDFDF';
                break;
    
            default:
                $color = '';
                $bgcolor = '';
                break;
        }
    
        echo '<div class="modal fade" id="msgModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content text-'.$color.'" style="background-color: '.$bgcolor.';">
                  <div class="modal-header border-'.$color.'">
                    <h5 class="modal-title" id="exampleModalLabel">'.$title.'</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    '.$message.'
                  </div>
                  <div class="modal-footer border-'.$color.'">
                    <button type="button" class="btn btn-'.$color.'" data-dismiss="modal">Fechar</button>
                  </div>
                </div>
              </div>
            </div>';
    
        echo '<script type="text/javascript">
        $(window).on("load",function(){
            $("#msgModal").modal("show");
        });
    </script>';
    }
    
    function sessionVerify()
        {
            session_start();
            if(!isset($_SESSION['logado'])){
                header('location: ../user/login.php');
            }
        }
    
        // Valida o username para permitir apenas alphanumericos, hifen, underline e ponto
        function filterName ($name, $filter = "[^a-zA-Z0-9\-\_\.]"){
        return preg_match("~" . $filter . "~iU", $name) ? false : true;
    }
    
    //função utilizada para verificar se a senha que o usuario informou coincide com a senha do bd.
    function newPasswordVerify($con, $currentPass, $newPass, $key)
    {
        session_start();
    
        if(isset($_SESSION['user'])){
            $idUser = $_SESSION['user']['idUser'];
    
            $sql = $con->prepare("SELECT senha_User FROM tbl_usuarios WHERE id_User = ?");
            $sql->bind_param("s", $idUser);
            $sql->execute();
    
            $get = $sql->get_result();
            $dados = $get->fetch_assoc();
            $senhaBD = decryptData($dados['senha_User'], $key);
            if($currentPass == $senhaBD){
                $get->free_result();
               return true;
            }else{
                $get->free_result();
                return false;
            }
        }
    }
    
    // Retorna os uploads separados em aprovados, pendentes e reprovados
    /* USO: $uploads = fetchUploads($con);
    while($row = $uploads['pendentes']->fetch_assoc())
    {
        echo $row['nomeJogo_Upload']; 
    }*/
    function fetchUploads($con)
    {
        $sql = "SELECT tbl_uploads.*, id_User, nome_User FROM tbl_uploads, tbl_usuarios WHERE id_User = idUser_Upload AND status_Upload = 'APROVADO'";
        $aprovados = $con->query($sql);
    
        $sql = "SELECT tbl_uploads.*, id_User, nome_User FROM tbl_uploads, tbl_usuarios WHERE id_User = idUser_Upload AND status_Upload = 'PENDENTE'";
        $pendentes = $con->query($sql);
    
        $sql = "SELECT tbl_uploads.*, id_User, nome_User FROM tbl_uploads, tbl_usuarios WHERE id_User = idUser_Upload AND status_Upload = 'REPROVADO'";
        $reprovados = $con->query($sql);
    
        return array("aprovados" => $aprovados,
            "pendentes" => $pendentes,
            "reprovados" => $reprovados);
        
        /* A resultado da query deve ser liberado apos o uso dos dados dessa funcao*/
    }
    
    /* Retorna QUANDO algo foi registrado formatado conforme o exemplo
    Ex: Mensagem: Recebido Hoje 16:54 
                           Ontem 13:15
                           25/04/2019 01:54
    */
    
    function getDateFormatedWhen($date)
    {
        $targetDate = new DateTime($date); // data passada como parametro
        $compareDate = new DateTime($targetDate->format('d-m-Y H:i:s')); // copia usada só para comparar
        $compareDate->modify('midnight');
        $currentDate = new DateTime(); // data atual do servidor
        $currentDate->modify('midnight');
    
    
        $diff = $compareDate->diff($currentDate);
    
        if($diff->d === 0 && $diff->m === 0 && $diff->y === 0) // se não passou nenhum dia, foi hoje
        {
            return 'Hoje '.$targetDate->format('H:i');
        }
        elseif($diff->d === 1 && $diff->m === 0 && $diff->y === 0) // Se passou apenas um dia, foi ontem
        {
            return 'Ontem '.$targetDate->format('H:i');
        }
        else
        {
            return $targetDate->format('d/m/Y');
        }
    }

    /* Se o texto de uma celula de uma tabela for muito longo, colocar ... no final (NAO EH NECESSARIAMENTE UMA TABELA */
    function sanitizeTableCellText($string, $maxlength)
    {
        if(strlen($string) > $maxlength)
        {
            $newString = substr($string, 0, $maxlength).'...';
        }
        else
        {
            $newString = $string;
        }
    
        return $newString;
    }

    //function pega qtd de topicos da que tem na categoria
    function topicosFromCategory($con, $idCategoria)
    {
        $sql = $con->prepare("SELECT COUNT(idCategoria_Topico) FROM tbl_topicos_forum WHERE idCategoria_Topico = ?");
        $sql->bind_param("s", $idCategoria);
        $sql->execute();
    
        $get = $sql->get_result();
        $topicos = $get->fetch_array();
    
        echo $topicos['0'];
    }
    
    //function exibe a quantidade de posts de uma categoria
    function postsFromCategory($con, $idCategoria)
    {
        $sql = $con->prepare("SELECT COUNT(idTopico_Post) FROM tbl_posts_forum WHERE (SELECT idCategoria_Topico FROM tbl_topicos_forum WHERE id_Topico = tbl_posts_forum.idTopico_Post) = ?");
        $sql->bind_param("s", $idCategoria);
        $sql->execute();
    
        $get = $sql->get_result();
        $posts = $get->fetch_array();
    
        echo $posts['0'];
    }
    
    // Mostra um BADGE com o numero de novas mensagens
    function showNewMessages($con, $idUser)
    {
        $sql = $con->prepare("SELECT COUNT(*) AS numNovasMensagens FROM tbl_mensagensPrivadas WHERE idDestinatario_Mensagem = ? AND visualizou_Mensagem = 0 AND destinatarioExcluiu_Mensagem = '0'");
        $sql->bind_param("i", $idUser);
        if($sql->execute())
        {
            $getResult = $sql->get_result();
            $result = $getResult->fetch_assoc();
            if($result['numNovasMensagens'] > 0)
            {
                $str = '<span class="badge badge-warning">'.$result['numNovasMensagens'].'</span>';
                $getResult->free_result();
                return $str;
            }
            else
            {
                $getResult->free_result();
                return '';
            }
        }
    }
    
    //ve se o email existe no banco
    function issetEmail($con, $email)
    {
        $sql = $con->prepare("SELECT email_User FROM tbl_usuarios WHERE id_User = ?");
        $sql->bind_param("s", $idUser);
        $sql->execute();
        $get = $sql->get_result();
        $verify = $get->fetch_assoc();

        if($verify['email_User'] == $email){
            echo $verify['email_User'];
        }else{
            echo $verify['email_User'];
        }
    }
    
    // Filtra uma url e retorna a url caso esteja validada, senao retorna false
    function filterUrl($url, $urlType = 'DEFAULT')
    {
        $sanitizedUrl = filter_var($url, FILTER_SANITIZE_URL);

        switch ($urlType) {
            case 'INSTAGRAM':
                if(!preg_match('/http(?:s)?:\/\/(?:www.)?instagram\.com\/([a-zA-Z0-9_]+)/', $sanitizedUrl))
                {
                    // se a url nao for do instagram, deixar false
                    $sanitizedUrl = false;
                }
                return $sanitizedUrl;
                break;

            case 'FACEBOOK':
                if(!preg_match('/http(?:s)?:\/\/(?:www.)?facebook\.com\/([a-zA-Z0-9_]+)/', $sanitizedUrl))
                {
                    // se a url nao for do facebook, deixar false
                    $sanitizedUrl = false;
                }
                return $sanitizedUrl;
                break;

            case 'TWITTER':
                if(!preg_match('/http(?:s)?:\/\/(?:www.)?twitter\.com\/([a-zA-Z0-9_]+)/', $sanitizedUrl))
                {
                    // se a url nao for do twitter, deixar false
                    $sanitizedUrl = false;
                }
                return $sanitizedUrl;
                break;

            case 'GITHUB':
                if(!preg_match('/http(?:s)?:\/\/(?:www.)?github\.com\/([a-zA-Z0-9_]+)/', $sanitizedUrl))
                {
                    // se a url nao for do twitter, deixar false
                    $sanitizedUrl = false;
                }
                return $sanitizedUrl;
                break;
            
            default:
                $sanitizedUrl = filter_var($sanitizedUrl, FILTER_VALIDATE_URL);
                return $sanitizedUrl;
                break;
        }
    }
    
    // Envia mensagem, OBS: esta funcao nao faz nenhuma validacao dos dados
    function sendMessage($con, $from, $to, $subject = '(Sem assunto)', $body)
    {
        $sql = $con->prepare("INSERT INTO tbl_mensagensPrivadas
      (idRemetente_Mensagem, idDestinatario_Mensagem, assunto_Mensagem, conteudo_Mensagem, dataEnvio_Mensagem)
        VALUES (?, ?, ?, ?, ?)");
        $sql->bind_param("iisss", $from, $to, $subject, $body, date('Y-m-d H:i:s'));
        if($sql->execute())
        {
            if($sql->affected_rows > 0)
            {
                return true;
            }
            return false;
        }
        else
        {
            return false;
        }
    }
    
    // deleta uma pasta e todos arquivos e sub pastas contidas nela
    function delTree($dir)
    { 
        $files = array_diff(scandir($dir), array('.', '..')); 

        foreach ($files as $file) { 
            (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file"); 
        }

        return rmdir($dir); 
    }
    
    /* Move uma pasta */
    function moveDir($source, $dest)
    {
    	// Ler os arquivos contidos na pasta
    	$files = scandir($source);
    
    	// para cada arquivo
    	foreach ($files as $file)
    	{
    	  // Se o arquivo for 'current' ou 'previous'
    	  if (in_array($file, array(".","..")))
    	  	continue; // pular para o prox
    
    	  // se nao existir a pasta destino, cria-la
    	  if(is_dir($dest)==false)
    	  {
    	  	mkdir($dest);
    	  }
    	  rename($source.$file, $dest.$file);
    	}
    	return rmdir($source);
    }
    
    //funcao para formatacao de texto
    function bbCode($str)
    {
        $str = strip_tags($str);
        $str = htmlentities($str);

        $tipos = array(
            '/\[b\](.*?)\[\/b\]/is',
            '/\[i\](.*?)\[\/i\]/is',
            '/\[u\](.*?)\[\/u\]/is',
            '/\[s\](.*?)\[\/s\]/is',
            //alinhamento
            '/\[center\](.*?)\[\/center\]/is',
            '/\[right\](.*?)\[\/right\]/is',
            '/\[justify\](.*?)\[\/justify\]/is',
            //imagem
            '/\[img\](.*?)\[\/img\]/is',
            //links
            '/\[url=(.*?)\](.*?)\[\/url\]/is',
            //listas
            '/\[list=(.*?)\](.*?)\[\/list\]/is',
            '/\[list\](.*?)\[\/list\]/is',
            '/\[\*\](.*?)(\n|\r\n?)/is',
            //tam. Fonte
            '/\[size=(.*?)\](.*?)\[\/size\]/is',
            //cor fonte
            '/\[color=(.*?)\](.*?)\[\/color\]/is',
            //code
            '/\[code\](.*?)\[\/code\](.*?)/is',
            '/\[block\](.*?)\[\/block\](.*?)/is',
            //tabulacao
            '/\[tab\](.*?)\[\/tab\](.*?)/is',
        );

        $replace = array(
            '<b>$1</b>',
            '<i>$1</i>',
            '<u>$1</u>',
            '<s>$1</s>',
            //alinhamento
            '<center>$1</center>',
            '<span class="float-right">$1</span>',
            '<span style="text-align: justify; text-justify: inter-word;">$1</span>',
            //imagem
            '<img class="img-fluid" src="$1">',
            // link
            '<a href="$1" target="_blank">$2</a>',
            //lista
            '<ol start=$1>$2</ol>',
            '<ul>$1</ul>',
            '<li>$1</li>',
            //tam. Fonte
            '<span style="font-size: $1px;">$2</span>',
            //cor fonte
            '<span style="color:$1;">$2</span>',
            //code
            '<span>&lt;<span style="color: #f00;">$1</span>&gt;</span>',
            '<div class="code">$1</div>',
            //tabulacao
            '<span style="margin-left: 35px;">$1</span>'
        );

        return preg_replace($tipos, $replace, $str);
    }
    
    // criptografa uma cadeia de caracteres com dada 'chave'
    function encryptData($string, $key)
    {
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($string, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('md5', $ciphertext_raw, $key, $as_binary=true);
    
        return $ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );
    }

    // descriptografa uma cadeia de caracteres com a 'chave' que foi usada para criptografar
    function decryptData($string, $key)
    {
        $c = base64_decode($string);
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len=16);
        $ciphertext_raw = substr($c, $ivlen+$sha2len);
        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
        return $original_plaintext;
    }
    
    // POSTAGEM DO FORUM: verifica se um post pertence ao usuario passado (id)
    function idVerify($con, $id)
    {
        $sql = $con->prepare("SELECT idUser_Post FROM tbl_posts_forum WHERE idUser_Post = ?");
        $sql->bind_param("s", $id);
        if($sql->execute())
        {
            if($sql->num_rows > 0)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }
    
    // mostra em html os espaços em branco extras, tabulações e quebras de linha
    function showAbsoluteFormat($str)
    {
        $returnStr = str_replace("\t", "&emsp;", $str);
        $returnStr = str_replace("  ", "&ensp;", $returnStr);
        
        return nl2br($returnStr);
    }
?>