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
    $limite=50;
    if (strlen($texto) <= $limite) {
        return $texto;
    } else {
        return substr($texto, 0, $limite - 3) . '...';
    }
}

$generos=["ação","animação","aventura","biografia ou autobiografia","comedia","corrida","documentário","drama","esporte",
"estratégia","fantasia","ficcao cientifica","fps","historico","isekai","josei","literatura clássica","mecha","mmorpg","musical","nao ficcao",
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
$i=1;

foreach($obras_melhorA  as  $obra_melhorA){
    $imageWidth = 50;
    $imageHeight = 70;
    
    // Margens
    $marginTop = 10;
    $marginLeft = 10;
    $spacing = 5;
    
    if($obra_melhorA['ativo']==1 && $i<11){
    // Insere imagem no canto esquerdo
    $pdf->SetFont('Arial','b',18);
    $pdf->Cell(0,10,$i.'.',0,1,'L');
    $pdf->SetFont('Arial','',12);
    // Define margem esquerda para o texto
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $temporada_f=selecionarTemporada1($obra_melhorA['id_serie']);
    $episodio_f1=selecionarEpisodio1($temporada_f['id_temporada']);
    $sinopse_melhorA=limitarTexto($obra_melhorA['sinopse']);
    $genero_melhorA=limitarTexto($obra_melhorA['genero']);
    // Adiciona a imagem
    $pdf->Image($obra_melhorA['imagem'], $x, $y, $imageWidth, $imageHeight);

    // Posiciona o texto ao lado da imagem
    $pdf->SetXY($x + $imageWidth + 10, $y + 5);
    $pdf->Cell(0, 6,'Nome da obra:'.textoPDF($obra_melhorA['nome_serie']), 0, 1);

    $pdf->SetX($x + $imageWidth + 10);
    $pdf->Cell(0, 6,'Tipo da obra:'.textoPDF($obra_melhorA['tipo']), 0, 1);

    $pdf->SetX($x + $imageWidth + 10);
    $pdf->Cell(0, 6,'Genero da obra:'.textoPDF($genero_melhorA), 0, 1);

    $pdf->SetX($x + $imageWidth + 10);
    $pdf->Cell(0, 6,'Sinopse da obra:'.textoPDF($sinopse_melhorA), 0, 1);

    $nota = $episodio_f1['media_nota'] !== null ? number_format($episodio_f1['media_nota'], 1) : '0.0';

    $pdf->SetX($x + $imageWidth + 10);
    $pdf->Cell(0, 6,'Nota da obra:'.$nota.'/10', 0, 1);

    // Espaço em branco (3 linhas)
    $pdf->Ln(15);

    // Mover para próxima posição vertical (abaixo da imagem)
    $pdf->SetY($y + $imageHeight + 10);

    // Se estiver perto do fim da página, adiciona nova página
    if ($pdf->GetY() + $imageHeight > $pdf->GetPageHeight() - 20) {
        $pdf->AddPage();
    }
    $i++;
    }
}


foreach($generos  as  $generoObra){
    $pdf->SetFont('Arial','b',18);
    $pdf->Cell(0,10,'Relatorio do genero '.textoPDF($generoObra),0,1,'C');
    $pdf->SetFont('Arial','',12);
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
$i=1;
if(empty($obras_melhorA)){
    $pdf->Cell(0,10,textoPDF('--não tem obra do genero  '.$generoObra.' cadastrado--'),0,1,'C');
}
foreach($obras_melhorA  as  $obra_melhorA){
    $imageWidth = 50;
    $imageHeight = 70;
    
    // Margens
    $marginTop = 10;
    $marginLeft = 10;
    $spacing = 5;
    
    if($obra_melhorA['ativo']==1 && $i<11){
    // Insere imagem no canto esquerdo
    $pdf->SetFont('Arial','b',18);
    $pdf->Cell(0,10,$i.'.',0,1,'L');
    $pdf->SetFont('Arial','',12);
    // Define margem esquerda para o texto
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $temporada_f=selecionarTemporada1($obra_melhorA['id_serie']);
    $episodio_f1=selecionarEpisodio1($temporada_f['id_temporada']);
    $sinopse_melhorA=limitarTexto($obra_melhorA['sinopse']);
    $genero_melhorA=limitarTexto($obra_melhorA['genero']);
    // Adiciona a imagem
    $pdf->Image($obra_melhorA['imagem'], $x, $y, $imageWidth, $imageHeight);

    // Posiciona o texto ao lado da imagem
    $pdf->SetXY($x + $imageWidth + 10, $y + 5);
    $pdf->Cell(0, 6,'Nome da obra:'.textoPDF($obra_melhorA['nome_serie']), 0, 1);

    $pdf->SetX($x + $imageWidth + 10);
    $pdf->Cell(0, 6,'Tipo da obra:'.textoPDF($obra_melhorA['tipo']), 0, 1);

    $pdf->SetX($x + $imageWidth + 10);
    $pdf->Cell(0, 6,'Genero da obra:'.textoPDF($genero_melhorA), 0, 1);

    $pdf->SetX($x + $imageWidth + 10);
    $pdf->Cell(0, 6,'Sinopse da obra:'.textoPDF($sinopse_melhorA), 0, 1);

    $nota = $episodio_f1['media_nota'] !== null ? number_format($episodio_f1['media_nota'], 1) : '0.0';

    $pdf->SetX($x + $imageWidth + 10);
    $pdf->Cell(0, 6,'Nota da obra:'.$nota.'/10', 0, 1);

    // Espaço em branco (3 linhas)
    $pdf->Ln(15);

    // Mover para próxima posição vertical (abaixo da imagem)
    $pdf->SetY($y + $imageHeight + 10);

    // Se estiver perto do fim da página, adiciona nova página
    if ($pdf->GetY() + $imageHeight > $pdf->GetPageHeight() - 20) {
        $pdf->AddPage();
    }
    $i++;
    }
}
}



$pdf->Output('relatorio_obras.pdf','I');
?>