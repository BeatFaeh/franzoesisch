<?php
declare(strict_types=1);
final class QuizService {
 public function __construct(private mysqli $db){}
 public function build(array $card,string $direction='de-fr',array $topics=[]):array{
  $correct=trim((string)$card['antwort']);$column=$direction==='fr-de'?'wort_d':'wort_f';$answers=[$correct];
  $sql="SELECT DISTINCT $column antwort FROM franzoesisch_woerter_und_saetze WHERE TRIM($column)<>'' AND TRIM($column)<>?";
  $topicColumn=$direction==='fr-de'?'thema_f':'thema_d';if($topics){$quoted=array_map(fn($v)=>"'".$this->db->real_escape_string(trim((string)$v))."'",array_filter($topics,fn($v)=>trim((string)$v)!==''));if($quoted)$sql.=' AND '.$topicColumn.' IN ('.implode(',',$quoted).')';}$sql.=' ORDER BY RAND() LIMIT 3';
  $stmt=$this->db->prepare($sql);if($stmt){$stmt->bind_param('s',$correct);$stmt->execute();$r=$stmt->get_result();while($x=$r->fetch_assoc())if(!in_array($x['antwort'],$answers,true))$answers[]=$x['antwort'];$stmt->close();}
  $options=[];$key='';if(count($answers)===4){shuffle($answers);foreach(['A','B','C','D'] as $i=>$l){$options[$l]=$answers[$i];if($answers[$i]===$correct)$key=$l;}}return['options'=>$options,'correct_key'=>$key];
 }
}
