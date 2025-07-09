<?php
require_once "conexao.php";
require_once "funcao.php";

if ($_SERVER["REQUEST_METHOD"]=="POST"){
    if(isset($_FILES['imagem_obra'])){
        $arquivo=$_FILES['imagem_obra'];
        $pasta="img/";
        if($arquivo['error']){
            die("Falha ao enviar arquivo");
        }
        $nome_arquivo=$arquivo['name'];
        $novo_nomeArquivo=uniqid();
        
        $extensao=strtolower(pathinfo($nome_arquivo,PATHINFO_EXTENSION));
        if($extensao!="jpg" && $extensao!="png"){
            die("Tipo de arquivo não aceito");
        }
        $path=$pasta.$novo_nomeArquivo.".".$extensao;
        $deu_certo=move_uploaded_file($arquivo["tmp_name"],$path);
        if($deu_certo){
            echo "<p>Arquivo enviado com sucesso</p>";
        }else{
            die("Falha ao enviar arquivo");

        }

        if (!empty($_POST['genero'])) {
            $opcoesSelecionadas = $_POST['genero']; //  ISSO É UM ARRAY
    
            //  JUNTA TODOS OS VALORES EM UMA STRING SEPARADA POR VÍRGULAS
            $stringFinal = implode(', ', $opcoesSelecionadas);

            echo "Opções selecionadas: " . htmlspecialchars($stringFinal);
        } else {
        echo "Nenhuma opção foi selecionada.";
        }
        

        $tipo = $_POST['tipo'] ?? ''; //    USA VALOR VAZIO SE NÃO VIER NADA

        echo "Você selecionou: " . htmlspecialchars($tipo);

        $sinopse = $_POST['sinopse'] ?? '';

        echo "Mensagem recebida:<br>" . nl2br(htmlspecialchars($sinopse));

        $sql_add_obra="INSERT INTO serie(imagem ,genero,tipo,sinopse,nome_serie) VALUES (:imagem ,:genero,:tipo,:sinopse,:nome_serie)";
        $stmt=$pdo->prepare($sql_add_obra);
        $stmt->bindParam(":imagem",$path);
        $stmt->bindParam(":genero",$stringFinal);
        $stmt->bindParam(":tipo",$tipo);
        $stmt->bindParam(":nome_serie",$_POST["nome_obra"]);
        $stmt->bindParam(":sinopse",$sinopse);
        try{
            $stmt->execute();
            echo"Cliente cadastro com sucesso!";

            $sql_s_obra="SELECT id_serie FROM serie WHERE nome_serie=:nome_serie AND tipo=:tipo ORDER BY id_serie ASC LIMIT 1";
            $stmt=$pdo->prepare($sql_s_obra);
            $stmt->bindParam(":nome_serie",$_POST["nome_obra"]);
            $stmt->bindParam(":tipo",$tipo);
            
            $stmt->execute();
            $serie=$stmt->fetchAll();
            if(!$serie){
                die("Erro: nenhuma serie encontrado");
            }
            foreach($serie as $obra){
                $n_t="Temporada";
                $n_e="Episodio";
                $n_ef="Episodio Famtasma";
                
                $sql_add_t_f="INSERT INTO temporada( descrisao_tem,idserie) VALUES ( :descrisao_tem,:idserie)";
                $stmt=$pdo->prepare($sql_add_t_f);
                $stmt->bindParam(":descrisao_tem",$n_t);
                $stmt->bindParam(":idserie",$obra['id_serie']);
                try{
                    $stmt->execute();
                    $sql_s_obra="SELECT id_temporada FROM temporada WHERE idserie=:idserie ORDER BY id_temporada ASC LIMIT 1";
                    $stmt=$pdo->prepare($sql_s_obra);
                    $stmt->bindParam(":idserie",$obra['id_serie']);
                    try{
                        $stmt->execute();
                        $tep=$stmt->fetchAll();
                        foreach($tep as $tem){
                            
                            /*$sql_add_e_f="INSERT INTO episodio(descrisao_ep, titulo,idtemporada) VALUES (:descrisao_ep,:titulo,:idtemporada)";
                            $stmt=$pdo->prepare($sql_add_e_f);
                            $stmt->bindParam(":descrisao_ep",$n_e);
                            $stmt->bindParam(":titulo",$n_ef);
                            $stmt->bindParam(":idtemporada",$tem['id_temporada']);
                            $stmt->execute();*/
                            try{
                                inserirEpisodio($tem['id_temporada'],$n_e,$n_ef);
                                header('Location:principal.php');
                            }catch(PDOException $e){
                                error_log("Erro ao inserir cliente3:".$e->getMessage());
                                echo"Erro ao cadastrar cliente3.";
                            }
                        }
                    
                    }catch(PDOException $e){
                        error_log("Erro ao inserir cliente2:".$e->getMessage());
                        echo"Erro ao cadastrar cliente2.";
                    }
                
            

            

            }catch(PDOException $e){
                error_log("Erro ao inserir cliente:".$e->getMessage());
                echo"Erro ao cadastrar cliente.";
            }
        }
    }catch(PDOException $e){
        error_log("Erro ao inserir cliente:".$e->getMessage());
        echo"Erro ao cadastrar cliente.";
    }
}
}
?>