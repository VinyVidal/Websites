-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: 15-Jun-2019 às 14:33
-- Versão do servidor: 10.2.23-MariaDB
-- versão do PHP: 7.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u439571750_rgc`
--
CREATE DATABASE IF NOT EXISTS `rgc` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `rgc`;

DELIMITER $$
--
-- Procedures
--
CREATE PROCEDURE `sp_aprovaSolicitacao` (IN `_idUpload` INT, IN `_idUser` INT, IN `_dataAprovacao` TIMESTAMP)  BEGIN
  DECLARE _status varchar(10);

  SELECT status_Upload INTO _status FROM tbl_uploads WHERE id_Upload = _idUpload;

  IF _status = 'PENDENTE' THEN -- Só uploads pendentes podem ser aprovados
    -- Subtrai 1 do campo pendentes
    UPDATE tbl_usuarios SET jogosPendentes_User = jogosPendentes_User - 1 WHERE id_User = _idUser;

    -- Adciona 1 ao campo aprovados
    UPDATE tbl_usuarios SET jogosMural_User = jogosMural_User + 1 WHERE id_User = _idUser;

    -- Atualiza o status e a data
    UPDATE tbl_uploads SET status_Upload = 'APROVADO' WHERE id_Upload = _idUpload;
    UPDATE tbl_uploads SET dataAprovacao_Upload = _dataAprovacao WHERE id_Upload = _idUpload;
    UPDATE tbl_uploads SET dataReprovacao_Upload = NULL WHERE id_Upload = _idUpload;
    /* Depois disso, o usuario deve ser notificado e o jogo será automaticamente adicionado ao mural*/
    
  ELSE
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Erro, a solicitação já foi aprovada, ou ela foi reprovada!';
  END IF;
  
  END$$

CREATE PROCEDURE `sp_enviaJogo` (IN `_idUser` INT, IN `_nomeJogo` VARCHAR(50), IN `_descricaoJogo` VARCHAR(255), IN `_capaJogo` VARCHAR(150), IN `_arquivoJogo` VARCHAR(150), IN `_dataEnvio` TIMESTAMP)  BEGIN
        -- Verifica se o usuario já tem outra solicitação pendente ou nao
        DECLARE _numSolicitacoes int;
        DECLARE _nivelUser int;
        SELECT jogosPendentes_User INTO _numSolicitacoes FROM tbl_usuarios WHERE id_User = _idUser;
        SELECT nivel_User INTO _nivelUser FROM tbl_usuarios WHERE id_User = _idUser;
        -- Se ele for usuario comum e ja tiver uma solicitação pendente, lançar erro
        IF _nivelUser = 1 AND _numSolicitacoes > 0 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Voce já tem uma solicitação pendente! Aguarde ela ser avaliada por um admin.';
         -- Se ele for moderador ou maior e ja tiver 3 solicitação pendente, lançar erro
        ELSEIF _nivelUser > 1 AND _numSolicitacoes > 3 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Usuario já tem 3 solicitações pendentes!';
        ELSE -- Senao, inserir jogo pendente
            INSERT INTO tbl_uploads (idUser_Upload, nomeJogo_Upload, descricaoJogo_Upload, imgCapaJogo_Upload, arquivosJogo_Upload, dataEnvio_Upload)
            VALUES (_idUser, _nomeJogo, _descricaoJogo, _capaJogo, _arquivoJogo, _dataEnvio);
            SET @linhasAfetadas = ROW_COUNT();
            -- E incrementar o numero de solicitações pendentes do usuario
            IF @linhasAfetadas > 0 THEN
                UPDATE tbl_usuarios SET jogosPendentes_User = jogosPendentes_User + 1 WHERE id_User = _idUser;
            END IF;

        END IF;

    END$$

CREATE PROCEDURE `sp_reprovaSolicitacao` (IN `_idUpload` INT, IN `_idUser` INT, IN `_dataReprovacao` TIMESTAMP)  BEGIN
  DECLARE _status varchar(10);

  SELECT status_Upload INTO _status FROM tbl_uploads WHERE id_Upload = _idUpload;

  IF _status != 'REPROVADO' THEN
    -- Subtrai do campo pendentes ou aprovados, dependendo do status atual
    IF _status = 'PENDENTE' THEN
      UPDATE tbl_usuarios SET jogosPendentes_User = jogosPendentes_User - 1 WHERE id_User = _idUser;
    ELSE -- Senao sera 'APROVADO'
      UPDATE tbl_usuarios SET jogosMural_User = jogosMural_User - 1 WHERE id_User = _idUser;
    END IF;
    -- Adciona 1 ao campo reprovados
    UPDATE tbl_usuarios SET jogosReprovados_User = jogosReprovados_User + 1 WHERE id_User = _idUser;

    -- Atualiza o status e a data
  	UPDATE tbl_uploads SET status_Upload = 'REPROVADO' WHERE id_Upload = _idUpload;
    UPDATE tbl_uploads SET dataAprovacao_Upload = NULL WHERE id_Upload = _idUpload;
    UPDATE tbl_uploads SET dataReprovacao_Upload = _dataReprovacao WHERE id_Upload = _idUpload;
  	/* Depois disso, o usuario deve ser notificado e o jogo será automaticamente removido do mural*/
    
  ELSE
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Erro, a solicitação já está reprovada!';
  END IF;
	
	END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbl_categorias_forum`
--

CREATE TABLE `tbl_categorias_forum` (
  `id_Categoria` int(11) NOT NULL,
  `idUser_Categoria` int(11) NOT NULL,
  `nome_Categoria` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `descricao_Categoria` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `dataCriacao_Categoria` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Extraindo dados da tabela `tbl_categorias_forum`
--

INSERT INTO `tbl_categorias_forum` (`id_Categoria`, `idUser_Categoria`, `nome_Categoria`, `descricao_Categoria`, `dataCriacao_Categoria`) VALUES
(1, 5, 'Regras e Comportamento', 'Aprenda como as damas e cavalheiros se comportam no fórum', '2019-06-08 01:20:06'),
(2, 1, 'Tutoriais', 'Tutoriais ensinando técnicas para a criação de jogos em HTML5', '2019-06-08 17:07:40'),
(3, 3, 'Introdução e Boas-vindas', 'Apresentação dos novos membros do fórum', '2019-06-11 10:52:13'),
(4, 3, 'Suporte', 'Tire suas duvidas sobre o uso do fórum e reporte qualquer problema aqui', '2019-06-11 11:12:40');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbl_mensagensPrivadas`
--

CREATE TABLE `tbl_mensagensPrivadas` (
  `id_Mensagem` int(11) NOT NULL,
  `idRemetente_Mensagem` int(11) NOT NULL,
  `idDestinatario_Mensagem` int(11) NOT NULL,
  `assunto_Mensagem` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '(Sem Assunto)',
  `conteudo_Mensagem` longtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `visualizou_Mensagem` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Se o destinatario viu a msg',
  `destinatarioExcluiu_Mensagem` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Se o destinatario exluiu a msg da Inbox',
  `dataEnvio_Mensagem` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Extraindo dados da tabela `tbl_mensagensPrivadas`
--

INSERT INTO `tbl_mensagensPrivadas` (`id_Mensagem`, `idRemetente_Mensagem`, `idDestinatario_Mensagem`, `assunto_Mensagem`, `conteudo_Mensagem`, `visualizou_Mensagem`, `destinatarioExcluiu_Mensagem`, `dataEnvio_Mensagem`) VALUES
(1, 1, 3, '(Sem assunto)', 'Vai tomar no cu', 1, 1, '2019-04-21 16:02:22'),
(3, 3, 5, 'Olá para voce amigo', 'Teste teste teste teste teste teste teste testeteste\r\ntestetestet estetestetesteteste teste teste testeteste\r\nteste teste teste teste testeteste teste teste teste v teste teste teste teste teste teste teste\r\ntestetestetestetestetestetestetestetestetesteteste', 1, 1, '2019-04-23 02:18:56'),
(4, 5, 1, 'Testando data e tamanho do assunto asd asd asd asd', 'asdas ads', 1, 1, '2019-04-23 20:31:08'),
(5, 3, 1, 'Uma Mensagem para voce', 'Nao quero saber.', 1, 1, '2019-04-25 14:30:39'),
(6, 1, 12, 'Boas vindas', 'Novato idiota', 0, 0, '2019-04-27 14:30:02'),
(7, 1, 5, 'Jogo aprovado (MENSAGEM AUTOMÁTICA)', 'O seu jogo (Teste Mensagem) foi aprovado e já está jogável no mural do site!<br>\r\n                      <a href=\"../mural/jogar.php?jogo=6\">Clique aqui</a> para ver o seu jogo.<br>\r\n                      <a href=\"../mural/jogos.php\">Clique aqui</a> para ver o mural de jogos.', 1, 0, '2019-04-28 10:26:19'),
(8, 3, 2, 'Teste', 'envio de mensagem', 1, 0, '2019-05-13 21:17:02'),
(9, 1, 3, 'Jogo excluído (MENSAGEM AUTOMÁTICA)', 'O seu jogo [ Teste ] foi excluido do mural pelo seguinte motivo:<br>\r\n              <i>Jogo de idiota</i><br>', 1, 0, '2019-05-13 21:24:21'),
(10, 1, 5, 'Teste espaço', 'Aqui as mensagens estão dando certo.\r\nEu acho.\r\nPq no forum não?', 1, 0, '2019-05-15 19:09:39'),
(11, 1, 2, 'Teste2', 'TEste 2\r\n\r\n\r\n\r\nTeste 44 44 4 4 4 44;.\r\n\r\nasdasd', 1, 0, '2019-05-15 19:10:10'),
(12, 1, 5, 'O que ta acontecendo com o nosso SITE?????', 'Parece que voltou por enquanto....', 0, 1, '2019-05-27 17:40:18'),
(13, 3, 1, 'Mensagens sem VIEW', 'Parece que ta dando certo', 1, 0, '2019-05-28 12:35:52'),
(14, 3, 2, 'blsalbla', 'fdsfdsfdsf', 0, 0, '2019-06-10 19:54:48'),
(15, 1, 3, 'Jogo enviado reprovado (MENSAGEM AUTOMÁTICA)', 'O seu jogo [ Minha Aventura ] foi reprovado pelo seguinte motivo:<br>\r\n                  <i>Era só um teste essa solic.</i><br>\r\n                  Leia as regras de envio de jogos no forum para saber como enviar um jogo no formato correto!', 1, 1, '2019-06-12 18:44:31'),
(16, 3, 1, 'Jogo enviado reprovado (MENSAGEM AUTOMÁTICA)', 'O seu jogo [ jogo ] foi reprovado pelo seguinte motivo:<br>\r\n                  <i>g</i><br>\r\n                  Leia as regras de envio de jogos no forum para saber como enviar um jogo no formato correto!', 0, 0, '2019-06-12 19:05:19'),
(17, 3, 1, 'Jogo enviado reprovado (MENSAGEM AUTOMÁTICA)', 'O seu jogo [ asd(REPROVADO) ] foi reprovado pelo seguinte motivo:<br>\r\n                  <i>sdsdsd</i><br>\r\n                  Leia as regras de envio de jogos no forum para saber como enviar um jogo no formato correto!', 1, 0, '2019-06-12 19:06:20');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbl_posts_forum`
--

CREATE TABLE `tbl_posts_forum` (
  `id_Post` int(11) NOT NULL,
  `idUser_Post` int(11) NOT NULL,
  `idTopico_Post` int(11) NOT NULL,
  `resposta_Post` tinyint(1) NOT NULL DEFAULT 1,
  `data_Post` timestamp NOT NULL DEFAULT current_timestamp(),
  `conteudo_Post` longtext COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Extraindo dados da tabela `tbl_posts_forum`
--

INSERT INTO `tbl_posts_forum` (`id_Post`, `idUser_Post`, `idTopico_Post`, `resposta_Post`, `data_Post`, `conteudo_Post`) VALUES
(1, 1, 1, 0, '2019-06-08 17:11:28', 'Primeira parte de uma série de tutoriais ensinando o básico de como criar games com HTML5.\r\n\r\nEae pessoal! Neste primeiro tutorial do fórum estarei dando introdução ao conceito de html5, javascript e canvas, necessários para inciar a criação de jogos para web. Essa série de tutoriais irá ensinar tudo o que você precisa saber para criar um jogo em HTML5, mesmo que você não tenha nenhuma noção de como fazer isto.\r\n\r\n[size=36][b]Um breve aviso[/b][/size]\r\nPrimeiramente, deixarei bem claro que criar jogos [b]NÃO É FÁCIL[/b]. O modo de criação que eu irei ensinar não envolve nenhum processo de arrastar e soltar objetos para ir montando o cenário do jogo, será tudo feito por código(explicarei mais adiante). Claro que existe ferramentas intuitivas que auxiliam muito na criação de jogos, mas não abordarei isso nesse tutorial.\r\nEntão você deve estar preparado para ler este artigo calmamente e tentando aprender o máximo que puder. Sem mais enrolações, vamos lá!\r\n\r\n[size=36][b]O que é HTML5?[/b][/size]\r\nHTML significa [i]Hypertext Markup Language[/i](Linguagem de Marcação de Hipertexto). É basicamente uma linguagem usada para construir estrutura de sites (páginas da web), sendo interpretada pelo navegador(Internet Explorer, Mozilla e Google Chrome).\r\nComo um exemplo, o código html se parece com o seguinte:\r\n\r\n[block][code]html[/code]\r\n[code]head[/code]\r\n	[code]meta charset=[color=#008000]\"UTF-8\"[/color][/code]\r\n	[code]title[/code] Meu primeiro Código HTML [code]/title[/code]\r\n[code]/head[/code]\r\n[code]body[/code]\r\n	Texto Básico em html.[code]br[/code]\r\n	Estamos numa nova linha![code]br[/code]\r\n[code]/body[/code]\r\n[code]/html[/code]\r\n[/block]\r\nComo você pode ver, a linguagem html utiliza palavras-chave entre < >, elas são chamadas de TAGS e cada uma tem um efeito na página:\r\n[list]\r\n[*]A tag [code]html[/code] indica o inicio do documento e [code]/html[/code] indica o fim do documento;\r\n[*] O texto entre as tags [code]title[/code] [code]/title[/code] aparecerá no titulo da guia(ou aba) do navegador;\r\n[*] Tudo dentro da tag [code]body[/code][code]/body[/code] aparecerá como conteúdo da página;\r\n[*] A [code]br[/code] serve para \'pular\' para a próxima linha.\r\n[/list]\r\nEnfim, existem muitas tags, mas não iremos nos aprofundar tanto assim.\r\n\r\nE como  fazer para o navegador interpretar este código? De forma básica, basta digitar esse código num editor de texto (Notepad) e salvar no formato .html (Ex: nomeArquivo.html) e dar um duplo clique no arquivo salvo.\r\nAliás, você pode fazer isso agora! Copie o código acima, cole num editor de texto e salve na extensão .html. Depois de salvo, abra o arquivo (provavelmente abrindo o seu navegador padrão) e você verá a página com o texto escrito após o [code]body[/code].\r\n\r\n[size=36][b]Ok, e o que isso tem a ver com jogos?[/b][/size]\r\nO HTML5 é a quinta versão do HTML, nela foi introduzido uma tag chamada [code]CANVAS[/code], essa tag cria um espaço na página que pode ser desenhado e animado(movimentação do desenho), e é essa tag que nos permite criar jogos!\r\n\r\nVamos testar a funcionalidade do canvas? Usando o mesmo código de antes, coloque essa linha após o [code]body[/code] :\r\n\r\n[block][code]canvas style=[color=#008000]\"border: 1px solid black;\"[/color] id=[color=#008000]\"meuCanvas;\"[/color][/code] O seu navegador não suporta o recurso Canvas [code]/canvas[/code][/block]\r\n\r\n[b]Explicação do Código[/b]\r\n[list]\r\n[*][i]style[/i] é um atributo que pode ser colocado em quase todas as tags, ele serve para estilizar o elemento (colocar bordas, mudar a cor, mudar o espaçamento, etc). A estilização é feita com CSS (Cascading Style Sheets), que é uma linguagem feita especificamente para alterar a aparência das páginas HTML. Nesse caso, [color=#008000]\"border: 1px solid black;\"[/color] coloca uma borda sólida de 1 [i]pixel[/i](px) de largura de cor preto;\r\n[*]O texto entre [code]canvas[/code] e [code]/canvas[/code] só será exibido quando o navegador executando a página não for compatível com o Canvas - versões mais antigas de navegadores podem não suportar o canvas.\r\n[*]Essa parte é importante! O atributo [i]ID[/i] é um apelido dado ao canvas, precisamos desse apelido para desenharmos no canvas mais pra frente!\r\n[/list]\r\nSalvando o arquivo e abrindo-o com o seu navegador resultará nisso:\r\n\r\n[img]https://i.imgur.com/Xqe02Iu.jpg[/img]\r\n\r\nIncrível, não? Esse quadro em branco é o nosso canvas, só esperando para ser \'pintado\' e animado!\r\nAliás, caso você ache que o canvas está muito pequeno, tente adicionar os atributos [color=#F00]height[/color] (altura) e [color=#F00]width[/color] (largura), especificando o tamanho em [i]pixels[/i]. Isso mudará a altura e largura da área do canvas!\r\n\r\n[block][code]canvas width=[color=#008000]\"600\"[/color] height=[color=#008000]\"500\"[/color] style=[color=#008000]\"border: 1px solid black;\"[/color] id=[color=#008000]\"meuCanvas\"[/color][/code] O seu navegador não suporta o recurso Canvas [code]/canvas[/code][/block]\r\n\r\nEu coloquei os atributos width e height antes do style, mas isso é indiferente, pois a ordem dos atributos não afeta em nada o resultado.\r\nVocê já deve ter percebido que o valor dos atributos sempre está contido entre aspas [b][i]\"\"[/i][/b], né? Isso é um padrão no HTML.\r\n\r\n[size=36][b]Como desenhar no Canvas?[/b][/size]\r\nAntes de prosseguir, eu preciso explicar como desenhar na área do canvas: usaremos uma linguagem de programação chamada JavaScript!\r\n\r\n[size=18][b]JavaScript[/b][/size]\r\nJavaScript é uma linguagem de programação usada em páginas da web para adicionar interações entre o site (ou página) e o usuário(você que está acessando a página).\r\nNós usaremos o JavaScript para desenhar no canvas, escrevendo linhas de código que orientam o navegador a criar as linhas e cores.\r\n\r\n[size=36][b]Preparando o espaço para programar com JavaScript[/b][/size]\r\nExistem duas formas de programar JavaScript numa página Html: a primeira é usando a tag [code]script[/code] e escrevendo o código JavaScript no próprio documento; a segunda também consiste em usar a tag [code]script[/code], mas o código é escrito num documento(arquivo) separado, deixando as coisas bem mais organizadas.\r\nDe início, usaremos a primeira forma, então vamos lá!\r\n\r\n[block][code]html[/code]\r\n[code]head[/code]\r\n	[code]meta charset=[color=#008000]\"UTF-8\"[/color][/code]\r\n	[code]title[/code] Meu primeiro Código HTML [code]/title[/code]\r\n[code]/head[/code]\r\n[code]body[/code]\r\n	[code]canvas width=[color=#008000]\"600\"[/color] height=[color=#008000]\"500\"[/color] style=[color=#008000]\"border: 1px solid black;\"[/color] id=[color=#008000]\"meuCanvas\"[/color][/code] O seu navegador não suporta o recurso Canvas [code]/canvas[/code]\r\n\r\n        * [code]script type=\"text/javascript\"[/code]\r\n           * [color=#808080]// Código JavaScript será escrito aqui![/color]\r\n       * [code]/script[/code]\r\n\r\n[code]/body[/code]\r\n[code]/html[/code]\r\n[/block]\r\n\r\n[b]OBS:[/b] Está vendo a porção do código com os asteriscos(*)? [b]Neste tutorial usarei os asteriscos para sinalizar partes novas do código. Então o * NÃO faz parte do código.[/b]\r\n\r\nPois bem, é dentro da tag [code]script[/code] que iremos programar o desenho do canvas com JavaScript.\r\nPercebeu as barras duplas (//) no código? Essas barras (uma seguida da outra) servem para [u]comentar[/u] o código, ou seja, escrever uma linha que não será interpretada pelo navegador, só serve como DOCUMENTAÇÃO do código. É uma boa prática comentar o que está sendo feito no código para você não se perder mais tarde!\r\n[b]Atenção:[/b] a tag [code]script[/code] deve ser inserida logo antes do [code]/body[/code], pois assim o código javascript só será executado quando todos os elementos da página forem carregados.\r\n\r\n[size=36][b]Próximos passos[/b][/size]\r\nPara não estender muito, finalizarei a primeira parte por aqui. No próximo post iremos desenhar figuras geométricas no canvas, pintaremos o fundo e começaremos a manipular o ambiente do canvas para então iniciarmos o nosso primeiro jogo!\r\nEspero que estejam gostando desse tutorial e aprendendo os conceitos! Deixe o seu comentário e faça perguntas sobre o assunto que eu responderei assim que puder, valeu!'),
(2, 1, 2, 0, '2019-06-08 17:13:06', 'Essa é a parte 2 do tutorial Criando jogo em HTML5. Veja a parte 1 [url=https://retrogamecenter.com.br/forum/topico.php?id=1]Introdução ao HTML5, JavaScript e Canvas[/url].\r\n\r\nNa postagem anterior aprendemos o que é HTML5, JavaScript e Canvas, e preparamos o nosso código para escrevermos em javascript.\r\nO código completo está assim:\r\n\r\n[block][code]html[/code]\r\n[code]head[/code]\r\n	[code]meta charset=[color=#008000]\"UTF-8\"[/color][/code]\r\n	[code]title[/code] Meu primeiro Código HTML [code]/title[/code]\r\n[code]/head[/code]\r\n[code]body[/code]\r\n        [code]script type=[color=#008000]\"text/javascript\"[/color][/code]\r\n           [color=#808080]// Código JavaScript será escrito aqui![/color]\r\n       [code]/script[/code]\r\n	[code]canvas width=[color=#008000]\"600\"[/color] height=[color=#008000]\"500\"[/color] style=[color=#008000]\"border: 1px solid black;\"[/color] id=[color=#008000]\"meuCanvas\"[/color][/code] O seu navegador não suporta o recurso Canvas [code]/canvas[/code]\r\n[code]/body[/code]\r\n[code]/html[/code]\r\n[/block]\r\n \r\nAgora iremos, utilizando a linguagem JavaScript, desenhar algumas figuras, pintar o fundo e mudar a cor dos elementos, tudo isso dentro do nosso canvas! Conforme estamos escrevendo o código, eu vou explicando as características do javascript e programação no geral.\r\n\r\n[size=36][b]Usando o \"Contexto\" do canvas[/b][/size]\r\nAntes de começarmos a desenhar, devemos \'pegar\' o contexto do canvas, que é um objeto que nos permite desenhar no canvas.\r\n\r\n[block][code]script[/code]\r\n	[color=#808080]// Código JavaScript será escrito aqui![/color]\r\n	[color=#00F]var[/color] canvas = document.getElementById([color=#008000]\"meuCanvas\"[/color]);\r\n	[color=#00F]var[/color] contexto = canvas.getContext([color=#008000]\"2d\"[/color]);\r\n[code]/script[/code][/block]\r\n\r\nExplicando o código:\r\n[list][*]\'var\' é uma palavra reservada da linguagem, ela é usada para definir uma \'[i]variável[/i]\';\r\n[*]Na maioria das linguagens de programação, uma variável é um espaço reservado para armazenar algum valor(número, texto e até representações de objetos);\r\n[*]Na primeira linha temos a instrução [i]document.getElementById(\"meuCanvas\")[/i], document representa a página da web em si e [i]getElementById[/i] é um método(ação) que \'pega\' um elemento que tenha a ID especificada. Então estamos armazenando na váriavel [i]canvas[/i] o elemento do canvas de id [i]\"meuCanvas\"[/i].\r\n[*][i]canvas.getContext(\"2d\")[/i] retorna o contexto do canvas, que nesso caso será um contexto em 2d(bidimensional). Existem outros contextos a serem usados nos canvas(3d, por exemplo), mas não vamos abordar isso no momento. Então a variável [i]contexto[/i] está armazenando o contexto 2d do nosso canvas.\r\n[*]Toda instrução deve terminar com ponto-e-virgula [i];[/i]\r\n[/list]\r\n\r\n[size=36][b]Agora sim, desenharemos![/b][/size]\r\nAgora iremos desenhar um quadrado(ou retângulo) no canvas! Para isso é necessário usarmos o método fillRect() do contexto, esse método desenha um quadrado/retângulo na posição desejada(dentro da área do canvas) e do tamanho desejado. \r\n\r\n[block][code]script[/code]\r\n	[color=#808080]// Código JavaScript será escrito aqui![/color]\r\n	[color=#00F]var[/color] canvas = document.getElementById([color=#008000]\"meuCanvas\"[/color]);\r\n	[color=#00F]var[/color] contexto = canvas.getContext([color=#008000]\"2d\"[/color]);\r\n	* contexto.fillRect(0, 0, 50, 50);\r\n[code]/script[/code][/block]\r\n\r\n[b]Lembrando que o asterisco (*) NÃO faz parte do código, só mostra o que foi adicionado![/b]\r\n\r\nO método [i]fillRect[/i] precisa receber quatro valores (chamados de argumentos) para desenhar: a posição x(eixo horizontal do canvas), a posição y(eixo vertical do canvas), o comprimento(ou largura) e por último a altura da figura geométrica.\r\n[b]OBS:[/b] A posição 0,0 (x=0 e y=0) corresponde à parte superior esquerda do canvas.\r\n\r\nApós escrever esse código, salvar o arquivo e executa-lo com o seu navegador, teremos isto:\r\n[img]https://i.imgur.com/EGHYUNM.jpg[/img]\r\n\r\nAlém disso, podemos desenhar apenas o contorno de um retângulo com [i]strokeRect[/i]\r\n[block][code]script[/code]\r\n	[color=#808080]// Código JavaScript será escrito aqui![/color]\r\n	[color=#00F]var[/color] canvas = document.getElementById([color=#008000]\"meuCanvas\"[/color]);\r\n	[color=#00F]var[/color] contexto = canvas.getContext([color=#008000]\"2d\"[/color]);\r\n	contexto.fillRect(0, 0, 50, 50);\r\n	* contexto.strokeRect(200, 100, 100, 50);\r\n[code]/script[/code][/block]\r\n[img]https://i.imgur.com/Yzbx2ml.jpg[/img]\r\n\r\n[size=36][b]Alterando a cor do contexto[/b][/size]\r\nPor padrão, as figuras são desenhadas na cor preto, mas podemos alterar isso com as propriedades(características ou informações de um objeto) [i]fillStyle[/i] (para alterar a cor do preenchimento) e [i]strokeStyle[/i](para alterar a cor do contorno). O valor necessário para mudar a cor é um [b]código HTML da cor[/b]. Por exemplo, o código da cor [color=#F00]vermelho[/color] é #FF0000, da cor preto é #000000, da cor branco é #FFFFFF e da cor [color=#00F]azul[/color] é #0000FF. [url=https://www.flextool.com.br/tabela_cores.html]Este link[/url] possui uma tabela com quase todas as cores e o seus códigos HTML.\r\n\r\n[block][code]script[/code]\r\n	[color=#808080]// Código JavaScript será escrito aqui![/color]\r\n	[color=#00F]var[/color] canvas = document.getElementById([color=#008000]\"meuCanvas\"[/color]);\r\n	[color=#00F]var[/color] contexto = canvas.getContext([color=#008000]\"2d\"[/color]);\r\n	contexto.fillRect(0, 0, 50, 50);\r\n	contexto.strokeRect(200, 100, 100, 50);\r\n	\r\n	* contexto.fillStyle = [color=#008000]\"#00FF00\"[/color];\r\n	* contexto.fillRect(35, 35, 60, 120);\r\n[code]/script[/code][/block]\r\nAssim teremos um retângulo verde em nosso canvas.\r\n\r\nSe você não quiser usar o código html das cores, pode escrever o nome da cor em [b]INGLÊS[/b], entre aspas duplas (SEMPRE), porém dessa forma você não terá acesso à muitas cores, só as básicas.\r\n[block]contexto.fillStyle = [color=#008000]\"green\"[/color];[/block]\r\nVale notar que quando o fillStyle ou strokeStyle é alterado, qualquer figura desenhada no resto do código assumirá a cor definida, até que ela seja alterada novamente.\r\nEntão se você definisse a cor do preenchimento para verde, qualquer figura desenhada após isso será verde.\r\n\r\n[img]https://i.imgur.com/16Qnquk.jpg[/img]\r\nVocê percebeu que o retângulo verde está sobrepondo o quadrado preto desenhado anteriormente? No canvas, a última figura desenhada SEMPRE sobreporá as outras.\r\n\r\n[size=36][b]\'Pintando\' o fundo do Canvas[/b][/size]\r\nNa verdade, alterar a cor do fundo do canvas nada mais é do que desenhar um retângulo que ocupa a área inteira do canvas.\r\nVamos deixar o fundo com a cor azul-cinza #CAE1FF.\r\n[block]contexto.fillStyle = [color=#008000]\"#CAE1FF\"[/color];\r\ncontexto.fillRect(0, 0, canvas.width, canvas.height);[/block]\r\nAs propriedades do canvas [b][i]\'width\'[/i][/b] e [b][i]\'height\'[/i][/b] são a largura e altura, respectivamente, do canvas. Então nesse código definimos o estilo de preenchimento para a cor azul-cinza e desenhamos um retângulo que inicia na posição 0,0 (canto superior esquerdo) que tem altura e largura igual à área do canvas (600 de largura e 500 de altura, nesse caso).\r\nO resultado será esse:\r\n[img]https://i.imgur.com/WFDev3N.jpg[/img]\r\n\r\n[size=36][b]Inserindo texto no canvas[/b][/size]\r\nTambém é possível desenhar texto no canvas, usando o método [i]fillText[/i] (texto com preenchimento) e [i]strokeText[/i] (texto só com o contorno). Vamos alterar a cor de preenchimento e contorno para vermelho e escrever no canvas!\r\n[block]contexto.fillStyle = [color=#008000]\"#FF0000\"[/color];\r\ncontexto.strokeStyle = [color=#008000]\"#FF0000\"[/color];\r\ncontexto.fillText([color=#008000]\"Olá Mundo!\"[/color], 40, 100);\r\ncontexto.strokeText([color=#008000]\"Segundo Texto\"[/color], 40, 200);[/block]\r\nComo você pode ver, para usar fillText e strokeText é necessário especificar o texto a ser escrito (sempre entre aspas duplas), além da posição x e y.\r\n[img]https://i.imgur.com/XtQX1Ib.jpg[/img]\r\nAchou o texto muito pequeno? Sem problemas, pois alterando a propriedade [i]\'font\'[/i] do contexto, podemos alterar o tamanho da fonte, junto com o estilo dela.\r\n[block]* contexto.font = [color=#008000]\"50px Arial\"[/color];\r\ncontexto.fillStyle = [color=#008000]\"#FF0000\"[/color];\r\ncontexto.strokeStyle = [color=#008000]\"#FF0000\"[/color];\r\ncontexto.fillText([color=#008000]\"Olá Mundo!\"[/color], 40, 100);\r\ncontexto.strokeText([color=#008000]\"Segundo Texto\"[/color], 40, 200);[/block]\r\nAssim a nossa fonte terá 50 [i]pixels[/i] de tamanho e o estilo [i]Arial[/i]. Semelhante à cor do contexto, quando a fonte é alterada, todo texto desenhado após isso terá esse mesmo estilo e tamanho.\r\n[img]https://i.imgur.com/cFAaPrT.jpg[/img]\r\n\r\n[size=36][b]Fim dos conceitos básicos[/b][/size]\r\nO canvas possui muitas funcionalidades, até agora vimos só algumas, mas na próxima postagem iremos parar de ver conceitos e iremos inciar o nosso joguinho! Estaremos aplicando tudo que vimos até agora e ainda aprenderemos mais coisas como eventos, [i]loop[/i] e condições. Até a próxima!'),
(3, 1, 3, 0, '2019-06-08 17:14:45', 'Parte 3 da série de tutoriais Criando jogo em HTML5. Se você ainda não viu as partes anteriores, confira!\r\n[url=https://retrogamecenter.com.br/forum/topico.php?id=1]Criando jogo em HTML5 PARTE 1 - Introdução ao HTML5, JavaScript e Canvas[/url]\r\n[url=https://retrogamecenter.com.br/forum/topico.php?id=2]Criando jogo em HTML5 PARTE 2 - Desenhando no Canvas com JavaScript[/url]\r\n\r\nNa postagem anterior, aprendemos a usar o contexto do canvas com javascript, desenhamos texto, retângulos e vimos como mudar a cor desses desenhos.\r\nAgora iremos inciar a criação do nosso primeiro jogo em HTML5!\r\n\r\n[size=36][b]Preparando o ambiente[/b][/size]\r\nAntes de iniciarmos a codificação do game, precisamos preparar os arquivos que iremos utilizar.\r\n\r\n[list][*]Crie uma pasta e renomeie-a para algo como \"meu_primeiro_jogo\" (sem as aspas);\r\n[*]Dentro desta pasta, crie um arquivo .html com o nome \"jogo\" (você pode abrir o seu editor de texto e ir em \"salvar como...\" para salvar o arquivo na extensão HTML);\r\n[*]Iremos trabalhar a programação do jogo em um arquivo javascript separado do documento html, então crie um arquivo .js chamado \'codigo\' (lembrando que não deve ter acentuação no nome do arquivo). [/list]\r\n[img]https://i.imgur.com/miPk9DN.jpg[/img]\r\n\r\nEm seguida precisamos estruturar a página html.\r\n\r\n[block][code]html[/code]\r\n[code]head[/code]\r\n	[code]meta charset=[color=#008000]\"UTF-8\"[/color][/code]\r\n	[code]title[/code] Meu primeiro jogo HTML5 [code]/title[/code]\r\n[code]/head[/code]\r\n[code]body[/code]\r\n	[code]canvas width=[color=#008000]\"800\"[/color] height=[color=#008000]\"500\"[/color] style=[color=#008000]\"border: 1px solid black;\"[/color] id=[color=#008000]\"canvasJogo\"[/color][/code] O seu navegador não suporta o recurso Canvas [code]/canvas[/code]\r\n\r\n* [code]script type=[color=#008000]\"text/javascript\"[/color] src=[color=#008000]\"codigo.js\"[/color][/code][code]/script[/code]\r\n\r\n[code]/body[/code]\r\n[code]/html[/code]\r\n[/block]\r\nNão é muito diferente do que fizemos nas partes anteriores deste tutorial, na verdade a maioria dos jogos contém uma página HTML bem simples, já que o código javascript externo será o \'coração\' do jogo.\r\nA novidade é a linha destacada pelo asterisco: a tag [code]script[/code] está requisitando(ou \'chamando\') um arquivo javascript externo que tenha o nome [color=#008000]\"codigo.js\"[/color], dessa forma os dois arquivos (o .html e o .js) estão ligados e qualquer código escrito no codigo.js será executado no documento HTML.\r\n\r\nCopie o código acima no arquivo jogo.html e salve-o, [b]lembre-se de remover o asterisco * pois ele não faz parte do código[/b]!\r\n\r\nAbra o codigo.js e vamos começar pegando o contexto do nosso canvas.\r\n[block][color=#00F]var[/color] canvas = document.getElementById([color=#008000]\"meuCanvas\"[/color]);\r\n[color=#00F]var[/color] ctx = canvas.getContext([color=#008000]\"2d\"[/color]);[/block]\r\nDesenhe um retângulo para testar o funcionamento do script. Adicione essa linha abaixo do código.\r\n[block]ctx.fillRect(0,0, 50, 50);[/block]\r\nSalve e execute o jogo.html, caso o retângulo não esteja aparecendo, quer dizer que algo deu errado. Volte atrás e tente seguir o passo-a-passo novamente.\r\n\r\nSe o retângulo estiver sendo desenhado, apague a linha que o desenha e podemos ir para o próximo passo!\r\n\r\n[size=36][b]Criando o nosso \"personagem\"[/b][/size]\r\nO personagem controlável do nosso jogo será bem simples: um quadrado azul capaz de se mover em qualquer direção. Então vamos começar desenhando-o no lado esquerdo da tela.\r\n\r\n[block]ctx.fillStyle = [color=#008000]\"#0000AA\"[/color];\r\nctx.fillRect(40, canvas.height/2 - 25, 50, 50);[/block]\r\nCriamos um quadrado de 50px ([i]pixels[/i]) com a posição x igual à 40 e a posição y igual à metade da altura do canvas subtraindo 25px. Veja o resultado abaixo:\r\n[img]https://i.imgur.com/Xh04Zw8.jpg[/img]\r\n\r\n[size=36][b]O [i]Loop[/i] do jogo[/b][/size]\r\nPara adicionarmos movimentação ao quadrado azul, primeiro precisamos criar o [i]loop[/i] do jogo.\r\nNo canvas, os objetos só são desenhados uma vez, porém num jogo precisamos que os objetos sejam desenhados constantemente, para assim criar a movimentação dos objetos.\r\n\r\nPara realizar o loop, usaremos o método [color=#F00]requestAnimationFrame[/color] do objeto window - que representa a janela do navegador. O [color=#F00]requestAnimationFrame[/color] requisita ao navegador o avanço de 1 [i]frame[/i], desta forma uma animação pode executar 1 frame, porém isso não é o suficiente para o nosso jogo, então usaremos juntamente uma \"função\" para chamar o método [color=#F00]requestAnimationFrame[/color] várias vezes.\r\n\r\n[color=#F00]Função (\"function\" na linguagem Javascript) é um bloco de código(uma ou varias linhas) que realiza uma ação sempre que forem \"chamadas\" (semelhante aos métodos que usamos).[/color]\r\n\r\n[color=#F00]requestAnimationFrame[/color] precisa de um argumento: um \"[i]callback[/i]\", que nada mais é do que uma função que será executada APÓS o frame de animação ser executado, dessa forma podemos criar um [i]callback[/i] que chama novamente o [color=#F00]requestAnimationFrame[/color], criando assim um loop(uma repetição infinita).\r\n\r\n[block][color=#00F]var[/color] loop = [color=#00F]function[/color]() {\r\n	[color=#808080]// Todo codigo escrito aqui será executado infinitamente[/color]\r\n	window.requestAnimationFrame(loop);\r\n}[/block]\r\nAgora temos o nosso \"[i]callback[/i]\"! Todo código escrito dentro dessa função será executado constantemente, e é exatamente isso que precisamos para o nosso jogo!\r\n\r\nPerceba que após escrever [color=#00F]function[/color](), devemos escrever o código que a função irá executar dentro de colchetes { }, as linhas de código escritas dentro dos colchetes se chamam \"bloco\". Usaremos mais disso durante a criação do nosso jogo.\r\n\r\nPara iniciar o loop, basta chamar o método window.requestAnimationFrame na linha seguinte e passar como argumento a variável loop (que guarda a nossa função de repetição).\r\n\r\n[block]window.requestAnimationFrame(loop);[/block]\r\nAgora coloque as linhas que desenham o quadrado azul(incluindo a troca da cor de preenchimento) DENTRO da função loop(), lembrando que o window.requestAnimationFrame deve ser a última linha do bloco!\r\n\r\n[block][color=#00F]var[/color] loop = [color=#00F]function[/color]() {\r\n	ctx.fillStyle = [color=#008000]\"#0000AA\"[/color];\r\n	ctx.fillRect(40, canvas.height/2 - 25, 50, 50);\r\n\r\n	window.requestAnimationFrame(loop);\r\n};\r\n[/block]\r\nPor enquanto o loop não afeta em nada o canvas, mas após adicionarmos movimento ao \"personagem\", veremos o que acontece!'),
(4, 1, 4, 0, '2019-06-08 17:15:04', 'Segue neste post regras e orientações de como enviar o seu jogo HTML5 para o nosso site, a fim de que ele seja exposto na seção do Mural aqui do Retro Game Center.\r\n\r\n[b][size=30]1. Zipando a pasta com os arquivos do seu jogo[/size][/b]\r\n[list][*]O seu jogo deve estar contido numa pasta com o nome do jogo(de preferência, sem espaços em branco para evitar quaisquer problemas);\r\n[*]O arquivo \'executável\' do jogo (o arquivo .html) deve estar localizado dentro dessa pasta com o nome do jogo, NÃO pode ter nenhuma subpasta entre a pasta principal e o .html;\r\n[*]Se tudo estiver assim, compacte a pasta no formato .ZIP (o programa WinRar pode fazer isso).[/list]\r\n\r\n[b][size=30]2. Escolhendo uma imagem de capa para o jogo[/size][/b]\r\nPara enviar o jogo, você precisara criar uma imagem de capa para o seu jogo, essa imagem irá representar a sua criação e será usada como capa nas páginas do site.\r\n\r\n[b][size=18]Como criar uma imagem de capa para o meu jogo?[/size][/b]\r\nVocê tem várias alternativas.\r\n[list][*]Tirar uma screenshot do seu jogo: esse é o metodo mais simples, basta tirar dar um printscreen na tela do seu jogo e ajustar o tamanho e a área a ser utilizada;\r\n[*]Tirar uma screenshot do menu principal do jogo: se o seu jogo tiver um menu principal, é uma boa idéa usa-lo como imagem de capa;\r\n[*]Usar uma logo: se você já criou uma logo para o seu jogo, é recomendável usa-la como imagem de capa. Caso você não tenha uma logo, mas queira uma, você pode cria-la gratuitamente em sites que criam diversas logos: basta escrever o nome do seu jogo como texto da logo e escolher o estilo que combina melhor com o seu jogo.\r\nEu recomendo [url=https://www.freelogoservices.com]esse site[/url] para criar logos para os seus jogos.[/list]\r\n\r\n[b][size=18]Qual deve ser o tamanho da imagem?[/size][/b]\r\nO tamanho recomendado para a imagem de capa é de 500 x 200 (500 pixels de largura e 200 pixels de altura). Você pode utilizar um editor de imagens (recomendo o Paint.NET) para redimensionar a imagem para um tamanho apropriado.\r\n\r\n[b][size=30]3. Enviando o jogo pelo site[/size][/b]\r\nAgora que os arquivos estão prontos, o seu jogo já pode ser enviado. Acesse a página [url=https://www.retrogamecenter.com.br/mural/enviarJogo.php]Envie o seu Jogo[/url] (você precisa estar cadastrado no nosso site)\r\n\r\n[img]https://i.imgur.com/m8WACRk.jpg[/img]\r\n\r\nPrimeiro insira o nome do jogo, o nome deve ser único e não muito longo.\r\n\r\nDepois, escreva uma descrição para o seu jogo, é bom resumir o seu jogo nesse campo e especificar as funcionalidades e controles (quais teclas são usadas para jogar o jogo).\r\n\r\nEm seguida, você deve fazer upload da imagem de capa e do arquivo .zip (passo 1 desta postagem). Basta escolher o arquivo do seu computador e clicar no botão Enviar para concluir o envio do jogo.\r\n\r\n[b]OBS:[/b] Ao enviar o arquivo ZIP do jogo, certifique-se de que não há nenhum arquivo pessoal dentro, pois esse arquivo .zip poderá ser visualizado e compartilhado pelos navegantes do site, afinal todos os jogos daqui são de código-livre (qualquer um pode ver o código do jogo).\r\n\r\nCom isso, o seu jogo será enviado para avaliação, dentro de 7 dias o seu jogo será analisado por nossa equipe para vermos se ele pode ser postado no mural.\r\n\r\nE é só isso! Simples, não? Lembrando que você só pode enviar um jogo de cada vez, ou seja, você só poderá enviar um segundo jogo, quando o primeiro terminar de ser avaliado!\r\nAssim acaba este guia, poste abaixo qualquer dúvida e aproveite o site!'),
(6, 3, 5, 0, '2019-06-11 15:42:25', 'Caros usuários,\r\nA lista a seguir abrange todas as regras de conduta do fórum e deve ser seguida por todos os membros e visitantes.\r\n[size=36][b]Regras Gerais do Fórum[/b][/size]\r\n[list]\r\n[*]Não poste links de sites inapropriados. Isto inclui, mas não se limita a, sites que contêm qualquer material adulto, informação sobre qualquer tipo de pirataria, material danoso, preconceito e/ou racismo.\r\n[*]Não poste imagens, vídeos, links ou qualquer outro do gênero que divulgue conteúdo inapropriado (nudez, fotos de outras pessoas, imagens que sugerem suicídio ou violência real, racismo, ódio ou material danoso ou que possa ofender).\r\n[*]Não poste materiais sobre sexo, racismo, discriminação ou ódio.\r\n[*]Não faça qualquer menção a planos, ideias, ou qualquer relação a atividades ilegais como roubo, drogas, assassinatos etc.\r\n[*]Não poste obscenidade.\r\n[*]Nunca insulte qualquer pessoa ou grupo.\r\n[*]Não crie/incentive discórdia de maneira direta ou indireta.\r\n[*]Não discuta religião, política ou orientação sexual.\r\n[*]Não aja de maneira grosseira, ofensiva ou que não seja cordial aos outros membros e equipe.\r\n[*]Não se passe por[b] Moderador[/b] ou [b]Administrador[/b] da [b]Retro Game Center[/b].\r\n[*]Não poste ou incentive Spams.\r\n[*]Não duplique tópicos.\r\n[*] Não crie tópicos redundantes.\r\n[*]Não poste tópicos mostrando conteúdo de bugs/ações ilegais, informe a equipe de[b] Moderadores[/b] ou [b]Administradores[/b] através da ferramenta de [b]mensagem[/b], a fim de que outros jogadores não se beneficiem com essas informações.\r\n[*]Não crie tópicos sobre a conduta de um [b]moderador[/b]. Informe a nossa equipe de [b]Administradores[/b] usando mensagens privadas.\r\n[*]Não tente acessar o computador de outros usuários passando informações no fórum. Não forneça seu login ou id de qualquer assunto pessoal no fórum.\r\n[*]Mantenha a sua educação. Evite fazer piadinhas ou diminuir tal pessoa pelo que ela postou ou como ela aparenta ser/escrever. Ofensas pessoais, flames e \"trollagens\" de mau gosto.\r\n[*]Não crie abaixo assinados, petições, correntes, campanhas ou qualquer outro do gênero.\r\n[*]Não poste múltiplas vezes o mesmo conteúdo ou tente trazer o seu/um tópico antigo para a primeira página.\r\n[*]Não crie intrigas e respeite a opinião de todos.\r\n[*]Tópicos que já receberam uma explicação oficial ou uma explicação correta não precisam mais ser respondidos.\r\n[*]Incitação e/ou apologia ao distúrbio da ordem do fórum. Arruaça, manifestações, intrigas, discussões e confusões no fórum se encaixam nela. Casos onde um usuário tenha o objetivo de causar confusões em demais membros e/ou atrapalhar a ordem entre membros do fórum.\r\n[*]Não poste ou crie tópicos sem conteúdo útil que possa levar um bom entretenimento a comunidade do [b]Retro Game Center[/b].\r\n[*]Não poste a mesma explicação em um tópico que já foi explicado seja de forma oficial ou por quaisquer usuários.. Uma vez explicado não há necessidade de explicar novamente.\r\n[*]Não use o fórum para se comunicar exclusivamente com outros membros. Use ferramenta [b]Mensagem Particular[/b] para isto.\r\n[*]Antes de criar um tópico, verifique se o mesmo assunto já existe.\r\n[*]Mantenha a formatação de seus tópicos/mensagens de maneira limpa e sem excessos.\r\n[*]Não poste piadas/imagens de mau gosto e/ou que possa ser ofensiva.\r\n[*]Não poste mensagens com uso excessivo de espaço.\r\n[*]Não poste ou crie tópicos para reclamar de um usuário, entre em contato com a [b]Moderação[/b] do fórum que irá tomar providências cabíveis.\r\n[*]Não crie tópicos sobre o mesmo assunto. Caso já exista um tópico oficial ou um tópico falando sobre o mesmo assunto que deseja criar utilize-o.\r\n[*]Não crie tópicos sobre assuntos já esclarecidos pela equipe [b]Retro Game Center[/b]. A criação dos mesmos resultará na remoção do conteúdo e no fechamento do tópico.\r\n[*]Qualquer tópico/mensagem sobre reclamações que não respeitar as regras do fórum, será tratado como qualquer outro tópico, lembrem-se de usar a área de reclamações com respeito as regras do fórum.\r\n[*]Não copie e poste artes/tutoriais e/ou qualquer outro tipo de conteúdo sem dar os devidos créditos ao criador. Este ato é considerado plágio e o usuário que for pego praticando tal será banido sem aviso prévio.\r\n[*]Postagem sem conteúdo - Todas as publicações devem ter um conteúdo útil. Isto é, uma mensagem a se passar. Postagens que evidentemente sejam FLOOD, podem acarretar em punições.\r\n[/list]\r\natt, [b]Equipe Retro Game Center[/b]'),
(7, 3, 6, 0, '2019-06-11 16:03:49', 'Caros usuários,\r\nNosso fórum foi desenvolvido com o intuito de passar conhecimento para nossa comunidade e tirar dúvidas dos mesmo, afim de manter nossa comunidade cada vez mais aconchegante e limpa para nossos membros e visitantes, a equipe é contra quaisquer tipos de preconceitos, racismo, ofensas e/ou qualquer outra atitude que possa ofender os membros do fórum ou a nossa equipe de Administradores e Moderadores.\r\nLeiam as [url=https://retrogamecenter.com.br/forum/topico.php?id=5]regras[/url] do fórum. Membros que forem pegos praticando estes atos serão punidos.\r\nRespeite a todos da nossa comunidade para que ela continue crescendo cada vez mais, afinal, estamos aqui para passar conhecimento e nos divertir!\r\natt, [b]Equipe Retro Game Center[/b].');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbl_topicos_forum`
--

CREATE TABLE `tbl_topicos_forum` (
  `id_Topico` int(11) NOT NULL,
  `idCategoria_Topico` int(11) NOT NULL,
  `idUser_Topico` int(11) NOT NULL,
  `titulo_Topico` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `tipo_Topico` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Discussão',
  `views_Topico` int(11) NOT NULL DEFAULT 0,
  `trancado_Topico` tinyint(1) NOT NULL DEFAULT 0,
  `dataCriacao_Topico` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Extraindo dados da tabela `tbl_topicos_forum`
--

INSERT INTO `tbl_topicos_forum` (`id_Topico`, `idCategoria_Topico`, `idUser_Topico`, `titulo_Topico`, `tipo_Topico`, `views_Topico`, `trancado_Topico`, `dataCriacao_Topico`) VALUES
(1, 2, 1, 'Criando jogo em HTML5 PARTE 1 - Introdução ao HTML5, JavaScript e Canvas', 'tutorial', 10, 0, '2019-06-08 17:11:28'),
(2, 2, 1, 'Criando jogo em HTML5 PARTE 2 - Desenhando no Canvas com JavaScript', 'tutorial', 30, 0, '2019-06-08 17:13:06'),
(3, 2, 1, 'Criando jogo em HTML5 PARTE 3 - Começando a criar o jogo!', 'tutorial', 51, 0, '2019-06-08 17:14:45'),
(4, 1, 1, 'Regras e Orientações para o Envio de Jogos', 'tutorial', 49, 0, '2019-06-08 17:15:04'),
(5, 1, 3, 'Regras Gerais', 'discussao', 16, 1, '2019-06-11 15:42:25'),
(6, 1, 3, 'Preconceito no Fórum', 'discussao', 18, 1, '2019-06-11 16:03:49');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbl_uploads`
--

CREATE TABLE `tbl_uploads` (
  `id_Upload` int(11) NOT NULL,
  `idUser_Upload` int(11) NOT NULL,
  `nomeJogo_Upload` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `descricaoJogo_Upload` varchar(2000) COLLATE utf8_unicode_ci NOT NULL,
  `imgCapaJogo_Upload` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `arquivosJogo_Upload` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `visualizacoesJogo_Upload` int(11) NOT NULL DEFAULT 0 COMMENT 'Visualizacoes do jogo aprovado',
  `status_Upload` varchar(10) COLLATE utf8_unicode_ci DEFAULT 'PENDENTE',
  `dataEnvio_Upload` timestamp NOT NULL DEFAULT current_timestamp(),
  `dataAprovacao_Upload` timestamp NULL DEFAULT NULL,
  `dataReprovacao_Upload` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Extraindo dados da tabela `tbl_uploads`
--

INSERT INTO `tbl_uploads` (`id_Upload`, `idUser_Upload`, `nomeJogo_Upload`, `descricaoJogo_Upload`, `imgCapaJogo_Upload`, `arquivosJogo_Upload`, `visualizacoesJogo_Upload`, `status_Upload`, `dataEnvio_Upload`, `dataAprovacao_Upload`, `dataReprovacao_Upload`) VALUES
(1, 1, 'Space Invaders', 'Reprodução do clássico do atari!\r\nJogue o clássico Space Invaders, do Atari 2600, agora no seu navegador!', 'uploads/aprovados/upload01/244f70ba36ed498720a09e9b22fced72.png', 'uploads/aprovados/upload01/655528f9d17df21a37ed40b1ebc1c2e9.zip', 114, 'APROVADO', '2019-04-17 14:12:21', '2019-04-17 14:14:26', NULL),
(21, 1, 'Ball Game', 'Jogo simples em que o objetivo é levar a bola para o topo da tela, enquanto evita os retângulos em movimento.\r\n\r\nControles no teclado:\r\nSetas direcionais - movimento da bola\r\n\r\nControles em dispositivos móveis:\r\nArraste na direção desejada para mover a bola.', 'uploads/aprovados/upload21/ba82e13e16eff0ba0600f89f5f484ee2.png', 'uploads/aprovados/upload21/a02502287eb59f5ae7b0c58a0700f701.zip', 43, 'APROVADO', '2019-05-22 20:11:20', '2019-05-22 20:11:42', NULL),
(23, 1, 'Pacman', 'O clássico Pacman reescrito para HTML5!\r\nCriado por Platzh1rsch\r\nGithub: https://github.com/platzhersh', 'uploads/aprovados/upload23/pacman.png', 'uploads/aprovados/upload23/9c8c610b6b1925438312c269d3ee1d8f.zip', 32, 'APROVADO', '2019-06-03 15:12:44', '2019-06-03 15:13:21', NULL),
(24, 1, 'Alien Invasion', 'Controle uma nave para acabar com a invasão alienígena e salvar o planeta Terra! \r\n\r\nCreditos para o usuário do Github cykod. Link: https://github.com/cykod/AlienInvasion', 'uploads/aprovados/upload24/6d9d4bd89b1dcae5c3c64aa7fffc27d7.jpg', 'uploads/aprovados/upload24/b5d87339852e0db447d26e61b05ea496.zip', 27, 'APROVADO', '2019-06-03 19:50:25', '2019-06-03 19:50:58', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbl_usuarios`
--

CREATE TABLE `tbl_usuarios` (
  `id_User` int(11) NOT NULL,
  `nomeCompleto_User` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `dataNasc_User` date NOT NULL,
  `email_User` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `nome_User` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `bio_User` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `senha_User` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `img_User` varchar(150) COLLATE utf8_unicode_ci DEFAULT 'img/padraoPerfil1.png',
  `nivel_User` int(11) DEFAULT 1,
  `forumPosts_User` int(11) DEFAULT 0,
  `jogosPendentes_User` int(11) DEFAULT 0,
  `jogosMural_User` int(11) DEFAULT 0,
  `jogosReprovados_User` int(11) DEFAULT 0,
  `link1_User` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'instagram',
  `link2_User` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'facebook',
  `link3_User` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'twitter',
  `link4_User` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'github',
  `dataCadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Extraindo dados da tabela `tbl_usuarios`
--

INSERT INTO `tbl_usuarios` (`id_User`, `nomeCompleto_User`, `dataNasc_User`, `email_User`, `nome_User`, `bio_User`, `senha_User`, `img_User`, `nivel_User`, `forumPosts_User`, `jogosPendentes_User`, `jogosMural_User`, `jogosReprovados_User`, `link1_User`, `link2_User`, `link3_User`, `link4_User`, `dataCadastro`) VALUES
(1, 'Vinicius Vidal', '1231-03-12', 'vinicius.vidalp@hotmail.com', 'viny_vidal', 'Meu nome é Vinicius, quero ser desenvolvedor de jogos para Web e Mobile.', '5PYnKbTKbTvbmnD9ojxAh2ffhJHv5dzN+X2q0R8n6I5vqP/dzifwzBnTWjee32St', 'img/perfil/viny_vidal.jpg', 3, 0, 0, 4, 20, '', 'https://www.facebook.com/fulano.detal', '', 'https://github.com/VinyVidal', '2019-04-10 00:58:23'),
(2, 'Bruno Ferreira', '1996-02-23', 'brunoferreira81@hotmail.com', 'Bruno', 'Sou viado.', 'e5PbG6yQcl1e4TWHhWy8q0iYhqMh+k+/4/a8uQTeL/kHuAJxX6hdLiuJTdSPWLzI', 'img/perfil/Bruno.png', 3, 0, 0, 0, 0, '', 'https://www.facebook.com/people/Matheus-Fernandes/100023667070912', '', '', '2019-04-10 00:59:27'),
(3, 'Retro Game center', '2019-04-11', 'retrog.center@gmail.com', 'RetroMaster', 'Usuário de controle do site', 'i/wTOxGFpkXasaBqt6B/IJTjCL0Sd6XB1qKVMLnV885F7fv5MfRmJ3KCDn11CpQg', 'img/perfil/RetroMaster.png', 4, 0, 0, 0, 7, 'https://www.instagram.com/rgc_media', '', '', '', '2019-04-10 01:02:33'),
(4, 'Michelle', '2001-04-08', 'michelleferreira2001@outlook.com', 'ParkJ', NULL, '45471065', 'img/padraoPerfil1.png', 1, 0, 0, 0, 0, NULL, NULL, NULL, NULL, '2019-04-10 01:49:48'),
(5, 'Matheus Fernandes Ventura', '2002-06-22', 'matheus.ventura01@etec.sp.gov.br', 'theo', 'Mano eu to achando a telinha de esqueceu senha mt lindinha kkkkk', 'ZBVO69VdToLKRZitzvXn73TzHnCT9MJLQJEuMJ5Zp3MHZdmSJiv6R6HE4dLUyA6U', 'img/perfil/theo.jpg', 3, 0, 0, 1, 1, NULL, NULL, NULL, NULL, '2019-04-10 03:43:06'),
(6, 'Douglas Dos Santos', '1975-08-27', 'profdouglassantos@gmail.com', 'dougbanner', NULL, '270875ab@', 'img/padraoPerfil1.png', 1, 0, 0, 0, 0, NULL, NULL, NULL, NULL, '2019-04-10 22:18:00'),
(7, 'Testando Completo', '3234-03-04', 'asd@asd', 'Testando', NULL, 'teste123', 'img/padraoPerfil1.png', 1, 0, 0, 0, 0, NULL, NULL, NULL, NULL, '2019-04-13 17:15:21'),
(8, 'Irineu', '2019-04-15', 'juliocesarpereira2000@gmail.com', 'Irineu', NULL, 'aaaaaaaaaaaa', 'img/padraoPerfil1.png', 1, 0, 0, 0, 0, NULL, NULL, NULL, NULL, '2019-04-16 02:43:58'),
(9, 'TestandoData Cadastro', '1231-03-12', 'asd@asdadasd', '123', NULL, '11111111', 'img/padraoPerfil1.png', 1, 0, 0, 0, 0, NULL, NULL, NULL, NULL, '2019-04-27 14:27:59'),
(10, 'José Ricardo Batista', '1992-08-29', 'ricardobaptistaal@gmail.com', 'Ricardo', NULL, 'jrba12tfba', 'img/perfil/Ricardo.jpg', 1, 0, 0, 0, 0, NULL, NULL, NULL, NULL, '2019-05-13 14:07:20'),
(11, 'Danilo Brandão De Oliveira', '1997-01-19', 'dandan4141@gmail.com', 'Danilo', NULL, '33bcd86e', 'img/padraoPerfil1.png', 1, 0, 0, 0, 0, NULL, NULL, NULL, NULL, '2019-05-13 19:40:56'),
(12, 'Teste', '2202-06-22', 'teste@gmail.com', 'Testeuser', NULL, '12345678', 'img/padraoPerfil1.png', 1, 0, 0, 0, 0, NULL, NULL, NULL, NULL, '2019-05-13 21:11:44'),
(13, 'Dadasdasdas', '4234-02-23', 'asdad@adsasa', 'cr', NULL, 'HMulBFlXiTgfU16wr0OOuXSLmMN1pHf+RhFRNY3YQKMyqfirKjfKf7eCTN+PNOsA', 'img/padraoPerfil1.png', 1, 0, 0, 0, 0, NULL, NULL, NULL, NULL, '2019-05-16 10:27:09'),
(14, 'Cript', '3213-02-23', 'vasada@asdadsd', 'cript', NULL, 'PK+/hbQQeIQhHtVlWPr5wHybHwtcdVIn5sFXcQOFaK4rvPepPxLPU6vPENkW+/zX', 'img/padraoPerfil1.png', 1, 0, 0, 0, 0, NULL, NULL, NULL, NULL, '2019-05-16 10:44:41'),
(15, 'Sla To Fazebd', '1111-11-11', 'acc.cf666@gmail.com', 'jaapago', NULL, '2FYQTEOknDOPSX9+dZw9nEp3CYJhn5eyF2u1At+uRbt14RuP+R5NdqyQ9M1jKPNA', 'img/padraoPerfil1.png', 1, 0, 0, 0, 0, NULL, NULL, NULL, NULL, '2019-06-11 11:15:25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_categorias_forum`
--
ALTER TABLE `tbl_categorias_forum`
  ADD PRIMARY KEY (`id_Categoria`),
  ADD KEY `idUser_Categoria` (`idUser_Categoria`);

--
-- Indexes for table `tbl_mensagensPrivadas`
--
ALTER TABLE `tbl_mensagensPrivadas`
  ADD PRIMARY KEY (`id_Mensagem`),
  ADD KEY `idRemetente_Mensagem` (`idRemetente_Mensagem`),
  ADD KEY `idDestinatario_Mensagem` (`idDestinatario_Mensagem`);

--
-- Indexes for table `tbl_posts_forum`
--
ALTER TABLE `tbl_posts_forum`
  ADD PRIMARY KEY (`id_Post`),
  ADD KEY `idUser_Post` (`idUser_Post`),
  ADD KEY `idTopico_Post` (`idTopico_Post`);

--
-- Indexes for table `tbl_topicos_forum`
--
ALTER TABLE `tbl_topicos_forum`
  ADD PRIMARY KEY (`id_Topico`),
  ADD KEY `idCategoria_Topico` (`idCategoria_Topico`),
  ADD KEY `idUser_Topico` (`idUser_Topico`);

--
-- Indexes for table `tbl_uploads`
--
ALTER TABLE `tbl_uploads`
  ADD PRIMARY KEY (`id_Upload`),
  ADD UNIQUE KEY `nomeJogo_Upload` (`nomeJogo_Upload`),
  ADD KEY `FK_idUser_Upload` (`idUser_Upload`);

--
-- Indexes for table `tbl_usuarios`
--
ALTER TABLE `tbl_usuarios`
  ADD PRIMARY KEY (`id_User`),
  ADD UNIQUE KEY `email_User` (`email_User`),
  ADD UNIQUE KEY `nome_User` (`nome_User`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_categorias_forum`
--
ALTER TABLE `tbl_categorias_forum`
  MODIFY `id_Categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_mensagensPrivadas`
--
ALTER TABLE `tbl_mensagensPrivadas`
  MODIFY `id_Mensagem` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `tbl_posts_forum`
--
ALTER TABLE `tbl_posts_forum`
  MODIFY `id_Post` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_topicos_forum`
--
ALTER TABLE `tbl_topicos_forum`
  MODIFY `id_Topico` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_uploads`
--
ALTER TABLE `tbl_uploads`
  MODIFY `id_Upload` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `tbl_usuarios`
--
ALTER TABLE `tbl_usuarios`
  MODIFY `id_User` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Limitadores para a tabela `tbl_mensagensPrivadas`
--
ALTER TABLE `tbl_mensagensPrivadas`
  ADD CONSTRAINT `tbl_mensagensPrivadas_ibfk_1` FOREIGN KEY (`idRemetente_Mensagem`) REFERENCES `tbl_usuarios` (`id_User`),
  ADD CONSTRAINT `tbl_mensagensPrivadas_ibfk_2` FOREIGN KEY (`idDestinatario_Mensagem`) REFERENCES `tbl_usuarios` (`id_User`);

--
-- Limitadores para a tabela `tbl_uploads`
--
ALTER TABLE `tbl_uploads`
  ADD CONSTRAINT `FK_idUser_Upload` FOREIGN KEY (`idUser_Upload`) REFERENCES `tbl_usuarios` (`id_User`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
