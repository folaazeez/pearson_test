<?php
namespace Pearson;

class ExamScoresManager
{
    private array $data_from_file;

    /** To add a new subject/scoring method, simply add a new line to the scoring_method array, 
     * listing the scoring methods from highest ranked to lowest ranked 
    **/
    private array $scoring_method= [
      "English"=>[8,7,6,5,4,3,2,1],
      "Maths"=>["A","B","C","D","E","F"],
      "Science"=>["Excellent","Good","Average","Poor","Very Poor"]
    ];

    /**
     * Constructor
     *
     * @param string $file_name
     * @return void
     */
    function __construct(string $file_name){

      $open = fopen($file_name, "r");

      fgetcsv($open,null, ","); //Read the first line so it is excluded from the rest of data
      
      while (($data = fgetcsv($open, null, ",")) !== FALSE) 
      {
        //Read each data line from the file
        $this->data_from_file[$data[0]][$data[1]][$data[4]][$data[2]]= $data[3];       
      }
      fclose($open);
    }

    /**
     * Converts the array data read from the scoring file into JSON 
     *
     * @return string
     */
    public function ConvertToJSON() : string{
        $json_Obj="[";

        foreach ($this->data_from_file as $Id=>$names){
            $json_Obj.='{
            "student_id": '.$Id.',';

            foreach ($names as $name=>$subjects){
                $json_Obj.='"name": "'.$name.'",';

                foreach ($subjects as $subject=>$learning_objectives){
                  $json_Obj.='"subject": "'.$subject.'",';
                  $json_Obj.='"scores": [';

                  $json_Obj.= $this->SortScores($subject,$learning_objectives);
        
                  $json_Obj.=']';
              }

            }

            $json_Obj.='},';
        
        }
        $json_Obj.="]";

        return $json_Obj;
    }

    /**
     * Sort the scores
     *
     * @param string $subject
     * @param array $learning_objectives
     * @return string
     */
    private function SortScores(string $subject,array $learning_objectives) : string{
      $json_Obj="";

      foreach ($learning_objectives as $learning_objective=>$score){
        //Holds the ranking for each score
        $r[]=array_search($score, $this->scoring_method[$subject]);
        //Holds the actual score value
        $s[]=$score;
        //Holds the learning_objective
        $a[]=$learning_objective;
      }
      
      //Sorts the arrays
      array_multisort($r, $s, $a);

      foreach ($r as $k=>$v){
          $json_Obj.='{"learning_objective":"'.$a[$k].'",';
          $json_Obj.='"score":'.(is_numeric($s[$k]) ? $s[$k] :'"'.$s[$k].'"').'},';
      }
      return $json_Obj;
    }

    /**
     * Dynamically add a new subject and scoring method
     *
     * @param string $subject
     * @param string $ranking
     * @return void
     */
    public function AddANewSubjectAndScoringMethod(string $subject, array $ranking) : void{
      /**To dynamically add a subject/scoring method
      $scoresObj->AddANewSubjectAndScoringMethod("Arts",["Distinction","Pass","Fail"]);
      **/
      array_push($this->scoring_method,[$subject=>$ranking]);
    }

}