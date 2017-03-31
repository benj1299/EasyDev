<?php
namespace ED\GeneratorBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class GeneratorValidation
{
    /**
     * @Assert\Length(max=255)
     * @Assert\NotBlank()
     */
    public $projectname;
    /**
     * @Assert\Type("bool")
     */
    public $check1;
    /**
     * @Assert\Type("bool")
     */
    public $check2;
    /**
     * @Assert\Type("bool")
     */
    public $check3;
    /**
     * @Assert\Type("bool")
     */
    public $check4;
    /**
     * @Assert\Type("bool")
     */
    public $check5;
    /**
     * @Assert\Type("bool")
     */
    public $check6;
    /**
     * @Assert\Type("bool")
     */
    public $check7;
    /**
     * @Assert\Type("bool")
     */
    public $check8;
    /**
     * @Assert\Type("string")
     */
    public $sql;
    public $bddname;
    public $bddid;
    public $bddpass;
    /*
     * @Assert\Collection(
     *  fields = {
     *     "html" = {
     *  @Assert\File(
     *               binaryFormat = false,
     *               maxSize = "40Mi",
     *               maxSizeMessage = "Le fichier est trop grand ({{ size }} {{ suffix }}). La taille maximum autorisée est de {{ limit }} {{ suffix }}.",
     *               disallowEmptyMessage = "Vous n'avez pas uploadé de fichier"
     *      )
     *  }
     * },
     *     allowMissingFields = true
     * )
     */
    public $files;
                                    /*GETTERS AND SETTERS*/

    /**
     * @return mixed
     */
    public function getProjectname()
    {
        return $this->projectname;
    }

    /**
     * @param mixed $projectname
     */
    public function setProjectname($projectname)
    {
        $this->projectname = $projectname;
    }

    /**
     * @return mixed
     */
    public function getCheck1()
    {
        return $this->check1;
    }

    /**
     * @param mixed $check1
     */
    public function setCheck1($check1)
    {
        $this->check1 = $check1;
    }

    /**
     * @return mixed
     */
    public function getCheck2()
    {
        return $this->check2;
    }

    /**
     * @param mixed $check2
     */
    public function setCheck2($check2)
    {
        $this->check2 = $check2;
    }

    /**
     * @return mixed
     */
    public function getCheck3()
    {
        return $this->check3;
    }

    /**
     * @param mixed $check3
     */
    public function setCheck3($check3)
    {
        $this->check3 = $check3;
    }

    /**
     * @return mixed
     */
    public function getCheck4()
    {
        return $this->check4;
    }

    /**
     * @param mixed $check4
     */
    public function setCheck4($check4)
    {
        $this->check4 = $check4;
    }

    /**
     * @return mixed
     */
    public function getCheck5()
    {
        return $this->check5;
    }

    /**
     * @param mixed $check5
     */
    public function setCheck5($check5)
    {
        $this->check5 = $check5;
    }

    /**
     * @return mixed
     */
    public function getCheck6()
    {
        return $this->check6;
    }

    /**
     * @param mixed $check6
     */
    public function setCheck6($check6)
    {
        $this->check6 = $check6;
    }

    /**
     * @return mixed
     */
    public function getCheck7()
    {
        return $this->check7;
    }

    /**
     * @param mixed $check7
     */
    public function setCheck7($check7)
    {
        $this->check7 = $check7;
    }

    /**
     * @return mixed
     */
    public function getCheck8()
    {
        return $this->check8;
    }

    /**
     * @param mixed $check8
     */
    public function setCheck8($check8)
    {
        $this->check8 = $check8;
    }

    /**
     * @return mixed
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * @param mixed $sql
     */
    public function setSql($sql)
    {
        $this->sql = $sql;
    }

    /**
     * @return mixed
     */
    public function getBddname()
    {
        return $this->bddname;
    }

    /**
     * @param mixed $bddname
     */
    public function setBddname($bddname)
    {
        $this->bddname = $bddname;
    }

    /**
     * @return mixed
     */
    public function getBddid()
    {
        return $this->bddid;
    }

    /**
     * @param mixed $bddid
     */
    public function setBddid($bddid)
    {
        $this->bddid = $bddid;
    }

    /**
     * @return mixed
     */
    public function getBddpass()
    {
        return $this->bddpass;
    }

    /**
     * @param mixed $bddpass
     */
    public function setBddpass($bddpass)
    {
        $this->bddpass = $bddpass;
    }

    /**
     * @return mixed
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param mixed $key
     * @param mixed $value
     */
    public function setFiles($key, $value)
    {
        $this->files[$key] = $value;
    }

}