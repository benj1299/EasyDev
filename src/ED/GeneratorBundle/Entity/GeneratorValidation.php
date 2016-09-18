<?php
namespace ED\GeneratorBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class GeneratorValidation
{
    /**
     * @Assert\Length(max=255)
     * @Assert\NotBlank()
     */
    protected $projectname;
    /**
     * @Assert\Type("bool")
     */
    protected $check1;
    /**
     * @Assert\Type("bool")
     */
    protected $check2;
    /**
     * @Assert\Type("bool")
     */
    protected $check3;
    /**
     * @Assert\Type("bool")
     */
    protected $check4;
    /**
     * @Assert\Type("bool")
     */
    protected $check5;
    /**
     * @Assert\Type("bool")
     */
    protected $check6;
    /**
     * @Assert\Type("bool")
     */
    protected $check7;
    /**
     * @Assert\Type("bool")
     */
    protected $check8;
    /**
     * @Assert\Type("string")
     */
    protected $packagist;
    protected $bddname;
    protected $bddid;
    protected $bddpass;
    /**
     * @Assert\File(
     *     binaryFormat = false,
     *     maxSize = "40Mi",
     *     mimeTypes = {"text/html"},
     *     mimeTypesMessage = "Veuillez entrer un fichier html valide",
     *     maxSizeMessage = "Le fichier est trop grand ({{ size }} {{ suffix }}). La taille maximum autorisée est de {{ limit }} {{ suffix }}.",
     *     disallowEmptyMessage = "Vous n'avez pas uploadé de fichier"
     * )
     */
    protected $htmlfile;
    /**
     * @Assert\File(
     *     binaryFormat = false,
     *     maxSize = "40Mi",
     *     mimeTypes = {"text/css"},
     *     mimeTypesMessage = "Veuillez entrer un fichier css valide",
     *     maxSizeMessage = "Le fichier est trop grand ({{ size }} {{ suffix }}). La taille maximum autorisée est de {{ limit }} {{ suffix }}.",
     * )
     */
    protected $cssfile;
    /**
     * @Assert\File(
     *     binaryFormat = false,
     *     maxSize = "40Mi",
     *     mimeTypes = {"text/javascript"},
     *     mimeTypesMessage = "Veuillez entrer un fichier javascript valide",
     *     maxSizeMessage = "Le fichier est trop grand ({{ size }} {{ suffix }}). La taille maximum autorisée est de {{ limit }} {{ suffix }}.",
     * )
     */
    protected $jsfile;

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
    public function getPackagist()
    {
        return $this->packagist;
    }

    /**
     * @param mixed $packagist
     */
    public function setPackagist($packagist)
    {
        $this->packagist = $packagist;
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
    public function getHtmlfile()
    {
        return $this->htmlfile;
    }

    /**
     * @param mixed $htmlfile
     */
    public function setHtmlfile($htmlfile)
    {
        $this->htmlfile = $htmlfile;
    }

    /**
     * @return mixed
     */
    public function getCssfile()
    {
        return $this->cssfile;
    }

    /**
     * @param mixed $cssfile
     */
    public function setCssfile($cssfile)
    {
        $this->cssfile = $cssfile;
    }

    /**
     * @return mixed
     */
    public function getJsfile()
    {
        return $this->jsfile;
    }

    /**
     * @param mixed $jsfile
     */
    public function setJsfile($jsfile)
    {
        $this->jsfile = $jsfile;
    }
}