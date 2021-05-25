<?php

namespace App\Validator;

use App\Service\ClienteHttp;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UrlAccesibleValidator extends ConstraintValidator
{

    private $clienteHttp;

    public function __construct(ClienteHttp $clienteHttp)
    {
        $this->clienteHttp = $clienteHttp;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\UrlAccesible */

        if (null === $value || '' === $value) {
            return;
        }


        $codigo = $this->clienteHttp->obtenerCodigoUrl($value);

        if($codigo === null) {
            $codigo = 'Error';
        }

        if($codigo != 200){
            $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $codigo)
            ->addViolation();
        }
      
    }
}
