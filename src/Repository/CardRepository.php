<?php
declare(strict_types=1);
final class CardRepository
{
    public function __construct(private mysqli $db) {}
    private function fields(string $direction): array { return $direction==='fr-de' ? ['wort_f','wort_d'] : ['wort_d','wort_f']; }
    private function topicColumn(string $direction): string { return $direction==='fr-de' ? 'thema_f' : 'thema_d'; }
    private function topicWhere(array $topics,string $direction): string {
        $topics=array_values(array_unique(array_filter(array_map(static fn($v):string=>trim((string)$v),$topics),static fn(string $v):bool=>$v!=='')));
        if(!$topics)return '';
        $quoted=array_map(fn(string $v):string=>"'".$this->db->real_escape_string($v)."'",$topics);
        return ' AND '.$this->topicColumn($direction).' IN ('.implode(',',$quoted).')';
    }
    public function random(string $direction='de-fr', array $topics=[]): ?array {
        [$q,$a]=$this->fields($direction);
        $sql="SELECT id,lektion,thema_d,thema_f,wort_d,wort_f,$q AS frage,$a AS antwort FROM franzoesisch_woerter_und_saetze WHERE TRIM($q)<>'' AND TRIM($a)<>''".$this->topicWhere($topics,$direction).' ORDER BY RAND() LIMIT 1';
        $r=$this->db->query($sql); return $r?($r->fetch_assoc()?:null):null;
    }
    public function randomMany(?int $limit,string $direction='de-fr',array $topics=[]): array {
        [$q,$a]=$this->fields($direction); $sql="SELECT id,lektion,thema_d,thema_f,wort_d,wort_f,$q AS frage,$a AS antwort FROM franzoesisch_woerter_und_saetze WHERE TRIM($q)<>'' AND TRIM($a)<>''".$this->topicWhere($topics,$direction).' ORDER BY RAND()';
        if($limit!==null)$sql.=' LIMIT '.max(1,$limit); $rows=[];$r=$this->db->query($sql);while($r&&$x=$r->fetch_assoc())$rows[]=$x;return$rows;
    }
    public function findById(int $id,string $direction='de-fr'): ?array {
        if($id<=0)return null;[$q,$a]=$this->fields($direction);$stmt=$this->db->prepare("SELECT id,lektion,thema_d,thema_f,wort_d,wort_f,$q AS frage,$a AS antwort FROM franzoesisch_woerter_und_saetze WHERE id=? LIMIT 1");if(!$stmt)return null;$stmt->bind_param('i',$id);$stmt->execute();$row=$stmt->get_result()->fetch_assoc();$stmt->close();return$row?:null;
    }
    public function searchByTerm(string $term,string $direction='de-fr',array $topics=[],int $limit=50): array {
        $term=trim($term);if($term==='')return[];[$q,$a]=$this->fields($direction);$limit=max(1,min(100,$limit));
        $sql="SELECT id,lektion,thema_d,thema_f,wort_d,wort_f,$q AS frage,$a AS antwort FROM franzoesisch_woerter_und_saetze WHERE (wort_d LIKE CONCAT('%',?,'%') OR wort_f LIKE CONCAT('%',?,'%') OR thema_d LIKE CONCAT('%',?,'%') OR thema_f LIKE CONCAT('%',?,'%'))".$this->topicWhere($topics,$direction)." ORDER BY CASE WHEN LOWER(TRIM(wort_d))=LOWER(TRIM(?)) OR LOWER(TRIM(wort_f))=LOWER(TRIM(?)) THEN 0 ELSE 1 END,lektion,id LIMIT $limit";
        $stmt=$this->db->prepare($sql);if(!$stmt)return[];$stmt->bind_param('ssssss',$term,$term,$term,$term,$term,$term);$stmt->execute();$r=$stmt->get_result();$rows=[];while($x=$r->fetch_assoc())$rows[]=$x;$stmt->close();return$rows;
    }
    public function count(string $direction='de-fr',array $topics=[]): int {[$q,$a]=$this->fields($direction);$sql="SELECT COUNT(*) anzahl FROM franzoesisch_woerter_und_saetze WHERE TRIM($q)<>'' AND TRIM($a)<>''".$this->topicWhere($topics,$direction);$row=$this->db->query($sql)?->fetch_assoc();return(int)($row['anzahl']??0);}
    public function topics(string $direction='de-fr'): array {$column=$this->topicColumn($direction);$rows=[];$r=$this->db->query("SELECT DISTINCT $column AS thema FROM franzoesisch_woerter_und_saetze WHERE TRIM($column)<>'' ORDER BY $column");while($r&&$x=$r->fetch_assoc())$rows[]=(string)$x['thema'];return$rows;}
    public function lessons(): array {$rows=[];$r=$this->db->query('SELECT DISTINCT lektion FROM franzoesisch_woerter_und_saetze ORDER BY lektion');while($r&&$x=$r->fetch_assoc())$rows[]=(int)$x['lektion'];return$rows;}
    public function adminSearch(string $term='',string $field='all',?int $limit=50): array {
        $term=trim($term);$where='';$params=[];$types='';
        if($term!==''){if($field==='id'&&ctype_digit($term)){$where=' WHERE id=?';$params=[(int)$term];$types='i';}elseif($field==='de'){$where=" WHERE wort_d LIKE CONCAT('%',?,'%')";$params=[$term];$types='s';}elseif($field==='fr'){$where=" WHERE wort_f LIKE CONCAT('%',?,'%')";$params=[$term];$types='s';}else{$where=" WHERE wort_d LIKE CONCAT('%',?,'%') OR wort_f LIKE CONCAT('%',?,'%') OR thema_d LIKE CONCAT('%',?,'%') OR thema_f LIKE CONCAT('%',?,'%')";$params=[$term,$term,$term,$term];$types='ssss';}}
        $sql='SELECT id,lektion,thema_d,thema_f,wort_d,wort_f FROM franzoesisch_woerter_und_saetze'.$where.' ORDER BY lektion,id'.($limit!==null?' LIMIT '.max(1,$limit):'');
        if(!$params){$r=$this->db->query($sql);}else{$stmt=$this->db->prepare($sql);if(!$stmt)return[];$stmt->bind_param($types,...$params);$stmt->execute();$r=$stmt->get_result();}
        $rows=[];while($r&&$x=$r->fetch_assoc())$rows[]=$x;if(isset($stmt))$stmt->close();return$rows;
    }
    public function adminCount(string $term='',string $field='all'): int {return count($this->adminSearch($term,$field,null));}
    public function add(string $wortD,string $wortF,string $themaD,string $themaF,int $lektion): bool {$stmt=$this->db->prepare('INSERT INTO franzoesisch_woerter_und_saetze (lektion,thema_d,thema_f,wort_d,wort_f) VALUES (?,?,?,?,?)');if(!$stmt)return false;$stmt->bind_param('issss',$lektion,$themaD,$themaF,$wortD,$wortF);$ok=$stmt->execute();$stmt->close();return$ok;}
    public function update(int $id,string $wortD,string $wortF,string $themaD,string $themaF,int $lektion): bool {$stmt=$this->db->prepare('UPDATE franzoesisch_woerter_und_saetze SET lektion=?,thema_d=?,thema_f=?,wort_d=?,wort_f=? WHERE id=?');if(!$stmt)return false;$stmt->bind_param('issssi',$lektion,$themaD,$themaF,$wortD,$wortF,$id);$ok=$stmt->execute();$stmt->close();return$ok;}
    public function delete(int $id): bool {$stmt=$this->db->prepare('DELETE FROM franzoesisch_woerter_und_saetze WHERE id=?');if(!$stmt)return false;$stmt->bind_param('i',$id);$ok=$stmt->execute();$stmt->close();return$ok;}
}
