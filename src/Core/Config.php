<?php


namespace Yoka\LianLianPay\Core;


use Yoka\LianLianPay\Support\Collection;

class Config extends Collection
{
    public function getInstantPayPrivateKey(): string
    {
        if (file_exists($this->get('private_key'))) {
            return file_get_contents($this->get('private_key'));
        } else {
            return $this->get('private_key');
        }
    }

    public function getInstantPayPublicKey(): string
    {
        if (file_exists($this->get('public_key'))) {
            return file_get_contents($this->get('public_key'));
        } else {
            return $this->get('public_key');
        }
    }

    public function getInstantPayLianLianPublicKey(): string
    {
        if (file_exists($this->get('ll_public_key'))) {
            return file_get_contents($this->get('ll_public_key'));
        } else {
            return $this->get('ll_public_key');
        }
    }
}