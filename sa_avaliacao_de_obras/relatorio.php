<?php
require_once 'conexao.php';
require_once 'funcao.php';

require 'fpdf/fpdf.php';
session_start();
if(!isset($_SESSION['id_usuario'])){
    header('Location:login.php');
exit();
}
class PDF extends FPDF{
    function Header(){
        $this->SetFont('Arial','B',20);
        $this->Cell(0,10,'Relatorio de Obras',0,1,'C');
        $this->Ln(10);

    }
    function Footer(){
        $this->SetY(-15);
        $this->SetFont('Arial','i',8);
        $this->Cell(0,10,'Pagina'.$this->PageNo(),0,0,'C');
    }
}
function textoPDF($txt){
    return iconv('UTF-8','ISO-8859-1//TRANSLIT',$txt);
}
function limitarTexto($texto) {
    $limite=60;
    if (strlen($texto) <= $limite) {
        return $texto;
    } else {
        return substr($texto, 0, $limite - 3) . '...';
    }
}

$generos=["ação","animação","aventura","biografia ou autobiografia","comedia","corrida","documentário","drama","esporte",
"estratégia","fantasia","ficcao cientifica","fps","historico","isekai","josei","literatura clássica","mecha","mmorpg","uusical","nao ficcao",
"plataforma","policial ou crime","puzzle","rpg","romance","seinen","shōjo","shōnen","simulação","slice of life","suspense ou thriller",
"terror ou horror","terro psicológico","terror de sobrevivencia"];

$pdf= new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial','',12);

$pdf->Cell(0,10,'Melhores Obras Avaliadas',0,1,'C');
$nome=' ';
$tipo=' ';
$genero=[];
$order='a';
$sql_obras_melhorA="SELECT s.*, ROUND(AVG(a.nota), 1) AS media_nota
FROM serie s
LEFT JOIN temporada t ON t.idserie = s.id_serie
LEFT JOIN episodio e ON e.idtemporada = t.id_temporada
LEFT JOIN avaliacao a ON a.idepisodio = e.id_episodio

GROUP BY s.id_serie  ORDER BY media_nota DESC ";
$stmt=$pdo->prepare($sql_obras_melhorA);

try{$stmt->execute();
    $obras_melhorA=$stmt->fetchAll(PDO::FETCH_ASSOC);
}catch(PDOException $e){
    error_log("Erro ao inserir cliente3:".$e->getMessage());
    echo"Erro ao cadastrar cliente3.";
}
$i=0;

foreach($obras_melhorA  as  $obra_melhorA){
    $marginLeft = 10;

    // Tamanho da imagem
    $imgWidth = 50;
    $imgHeight = 70; // ajuste se necessário

    // Posição atual Y
    $y = $pdf->GetY();

    // Insere imagem no canto esquerdo
  

    // Define margem esquerda para o texto
    $pdf->SetX($marginLeft);
    
    
    if($obra_melhorA['ativo']==1 && $i<11){
        $temporada_f=selecionarTemporada1($obra_melhorA['id_serie']);
        $episodio_f1=selecionarEpisodio1($temporada_f['id_temporada']);
        $sinopse_melhorA=limitarTexto($obra_melhorA['sinopse']);
        $pdf->Image($obra_melhorA['imagem'], $marginLeft, $y, $imgWidth, $imgHeight);
        $pdf->SetY($y + $imgHeight + 3);
        $pdf->SetX($marginLeft);
        $pdf->Cell(0,10,'nome da obra:'.textoPDF($obra_melhorA['nome_serie']),0,1,'L');
        $pdf->Cell(0,10,'tipo da obra:'.textoPDF($obra_melhorA['tipo']),0,1,'L');
        $pdf->Cell(0,10,'generos da obra:'.textoPDF($obra_melhorA['genero']),0,1,'L');
        $pdf->Cell(0,10,'sinopse da obra:'.textoPDF($sinopse_melhorA),0,1,'L');
        $nota = $episodio_f1['media_nota'] !== null ? number_format($episodio_f1['media_nota'], 1) : '0.0';
        $pdf->Cell(0,10,'nota da obra:'.$nota.'/10',0,1,'L');
        $i++;
    }
}

$pdf->Cell(0,10,'Melhores Obras Avaliadas',0,1,'C');
$nome=' ';
$tipo=' ';
$genero=[];
$order='a';


$sqlS_e="SELECT 
s.*,
COUNT(a.id_avaliacao) AS total_avaliacoes
FROM 
serie s
JOIN 
temporada t ON t.idserie = s.id_serie
JOIN 
episodio e ON e.idtemporada = t.id_temporada
LEFT JOIN 
avaliacao a ON a.idepisodio = e.id_episodio
GROUP BY 
s.id_serie, s.nome_serie
ORDER BY 
total_avaliacoes DESC";
    $stmt=$pdo->prepare($sqlS_e);
    try{$stmt->execute();
        $obras_maisA=$stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(PDOException $e){
            error_log("Erro ao inserir cliente3:".$e->getMessage());
            echo"Erro ao cadastrar cliente3.";
        }

$i=0;

foreach($obras_maisA  as  $obra_maisA){

    $marginLeft = 10;

    // Tamanho da imagem
    $imgWidth = 50;
    $imgHeight = 70; // ajuste se necessário

    // Posição atual Y
    $y = $pdf->GetY();

    // Insere imagem no canto esquerdo
  

    // Define margem esquerda para o texto
    $pdf->SetX($marginLeft);
    
    if($obra_maisA['ativo']==1 && $i<11){
        $temporada_f=selecionarTemporada1($obra_maisA['id_serie']);
        $episodio_f1=selecionarEpisodio1($temporada_f['id_temporada']);
        $sinopse_maisA=limitarTexto($obra_maisA['sinopse']);
        $pdf->Image($obra_maisA['imagem'], $marginLeft, $y, $imgWidth, $imgHeight);
        $pdf->SetY($y + $imgHeight + 3);
        $pdf->SetX($marginLeft);
        $pdf->Cell(0,10,'nome da obra:'.textoPDF($obra_maisA['nome_serie']),0,1,'L');
        $pdf->Cell(0,10,'tipo da obra:'.textoPDF($obra_maisA['tipo']),0,1,'L');
        $pdf->Cell(0,10,'generos da obra:'.textoPDF($obra_maisA['genero']),0,1,'L');
        $pdf->Cell(0,10,'sinopse da obra:'.textoPDF($sinopse_maisA),0,1,'L');
        $nota = $episodio_f1['media_nota'] !== null ? number_format($episodio_f1['media_nota'], 1) : '0.0';
        $pdf->Cell(0,10,'nota da obra:'.$nota.'/10',0,1,'L');
        $pdf->Cell(0,10,textoPDF('numero de avaliações da obra:').$obra_maisA['total_avaliacoes'],0,1,'L');

        $i++;
    }
}

foreach($generos  as  $generoObra){
    $pdf->Cell(0,10,'Relatorio do genero'.textoPDF($generoObra),0,1,'C');
    $pdf->Cell(0,10,'Melhores Obras Avaliadas do genero '.textoPDF($generoObra),0,1,'C');
$nome=' ';
$tipo=' ';
$genero=[];
$order='a';
$sql_obras_melhorA="SELECT s.*, ROUND(AVG(a.nota), 1) AS media_nota
FROM serie s
LEFT JOIN temporada t ON t.idserie = s.id_serie
LEFT JOIN episodio e ON e.idtemporada = t.id_temporada
LEFT JOIN avaliacao a ON a.idepisodio = e.id_episodio
WHERE s.genero LIKE :genero
GROUP BY s.id_serie  ORDER BY media_nota DESC ";
$stmt=$pdo->prepare($sql_obras_melhorA);
$genero_obra="%$generoObra%";
$stmt->bindParam(":genero",$genero_obra);

try{$stmt->execute();
    $obras_melhorA=$stmt->fetchAll(PDO::FETCH_ASSOC);
}catch(PDOException $e){
    error_log("Erro ao inserir cliente3:".$e->getMessage());
    echo"Erro ao cadastrar cliente3.";
}
$i=0;

foreach($obras_melhorA  as  $obra_melhorA){
    $marginLeft = 10;

    // Tamanho da imagem
    $imgWidth = 50;
    $imgHeight = 70; // ajuste se necessário

    // Posição atual Y
    $y = $pdf->GetY();

    // Insere imagem no canto esquerdo
  

    // Define margem esquerda para o texto
    $pdf->SetX($marginLeft);
    
    if($obra_melhorA['ativo']==1 && $i<11){
        $temporada_f=selecionarTemporada1($obra_melhorA['id_serie']);
        $episodio_f1=selecionarEpisodio1($temporada_f['id_temporada']);
        $sinopse_melhorA=limitarTexto($obra_melhorA['sinopse']);
        $pdf->Image($obra_melhorA['imagem'], $marginLeft, $y, $imgWidth, $imgHeight);
        $pdf->SetY($y + $imgHeight + 3);
        $pdf->SetX($marginLeft);
        $pdf->Cell(0,10,'nome da obra:'.textoPDF($obra_melhorA['nome_serie']),0,1,'L');
        $pdf->Cell(0,10,'tipo da obra:'.textoPDF($obra_melhorA['tipo']),0,1,'L');
        $pdf->Cell(0,10,'generos da obra:'.textoPDF($obra_melhorA['genero']),0,1,'L');
        $pdf->Cell(0,10,'sinopse da obra:'.textoPDF($sinopse_melhorA),0,1,'L');
        $nota = $episodio_f1['media_nota'] !== null ? number_format($episodio_f1['media_nota'], 1) : '0.0';
        $pdf->Cell(0,10,'nota da obra:'.$nota.'/10',0,1,'L');

        $i++;
    }
}

$pdf->Cell(0,10,'Obras mais avaliadas do genero '.textoPDF($generoObra),0,1,'C');
$nome=' ';
$tipo=' ';
$genero=[];
$order='a';


$sqlS_e="SELECT 
s.*,
COUNT(a.id_avaliacao) AS total_avaliacoes
FROM 
serie s
JOIN 
temporada t ON t.idserie = s.id_serie
JOIN 
episodio e ON e.idtemporada = t.id_temporada
LEFT JOIN 
avaliacao a ON a.idepisodio = e.id_episodio
WHERE s.genero LIKE :genero
GROUP BY 
s.id_serie, s.nome_serie
ORDER BY 
total_avaliacoes DESC";
    $stmt=$pdo->prepare($sqlS_e);
    $genero_obra="%$generoObra%";
    $stmt->bindParam(":genero",$genero_obra);
    try{$stmt->execute();
        $obras_maisA=$stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(PDOException $e){
            error_log("Erro ao inserir cliente3:".$e->getMessage());
            echo"Erro ao cadastrar cliente3.";
        }

$i=0;

foreach($obras_maisA  as  $obra_maisA){
    $marginLeft = 10;

    // Tamanho da imagem
    $imgWidth = 50;
    $imgHeight = 70; // ajuste se necessário

    // Posição atual Y
    $y = $pdf->GetY();

    // Insere imagem no canto esquerdo
  

    // Define margem esquerda para o texto
    $pdf->SetX($marginLeft);
    
    if($obra_maisA['ativo']==1 && $i<11){
        $temporada_f=selecionarTemporada1($obra_maisA['id_serie']);
        $episodio_f1=selecionarEpisodio1($temporada_f['id_temporada']);
        
        $sinopse_maisA=limitarTexto($obra_maisA['sinopse']);
        $pdf->Image($obra_maisA['imagem'], $marginLeft, $y, $imgWidth, $imgHeight);
        $pdf->SetY($y + $imgHeight + 3);
        $pdf->SetX($marginLeft);
        $pdf->Cell(0,10,'nome da obra:'.textoPDF($obra_maisA['nome_serie']),0,1,'L');
        $pdf->Cell(0,10,'tipo da obra:'.textoPDF($obra_maisA['tipo']),0,1,'L');
        $pdf->Cell(0,10,'generos da obra:'.textoPDF($obra_maisA['genero']),0,1,'L');
        $pdf->Cell(0,10,'sinopse da obra:'.textoPDF($sinopse_maisA),0,1,'L');
        $nota = $episodio_f1['media_nota'] !== null ? number_format($episodio_f1['media_nota'], 1) : '0.0';
        $pdf->Cell(0,10,'nota da obra:'.$nota.'/10',0,1,'L');
        $pdf->Cell(0,10,textoPDF('numero de avaliações da obra:').$obra_maisA['total_avaliacoes'],0,1,'L');


        $i++;
    }
}
}



$pdf->Output('relatorio_obras.pdf','I');
?>