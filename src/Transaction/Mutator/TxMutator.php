<?php

namespace BitWasp\Bitcoin\Transaction\Mutator;

use BitWasp\Bitcoin\Transaction\Transaction;
use BitWasp\Bitcoin\Collection\Transaction\TransactionInputCollection;
use BitWasp\Bitcoin\Transaction\TransactionInterface;
use BitWasp\Bitcoin\Collection\Transaction\TransactionOutputCollection;

class TxMutator
{
    /**
     * @var TransactionInterface
     */
    private $transaction;

    /**
     * @var InputCollectionMutator
     */
    private $inputsMutator;

    /**
     * @var OutputCollectionMutator
     */
    private $outputsMutator;

    /**
     * @param TransactionInterface $transaction
     */
    public function __construct(TransactionInterface $transaction)
    {
        $this->transaction = clone $transaction;
    }

    /**
     * @return InputCollectionMutator
     */
    public function inputsMutator()
    {
        if (is_null($this->inputsMutator)) {
            $this->inputsMutator = new InputCollectionMutator($this->transaction->getInputs()->all());
        }

        return $this->inputsMutator;
    }

    /**
     * @return OutputCollectionMutator
     */
    public function outputsMutator()
    {
        if (is_null($this->outputsMutator)) {
            $this->outputsMutator = new OutputCollectionMutator($this->transaction->getOutputs()->all());
        }

        return $this->outputsMutator;
    }

    /**
     * @return TransactionInterface
     */
    public function done()
    {
        if (!is_null($this->inputsMutator)) {
            $this->inputs($this->inputsMutator->done());
        }

        if (!is_null($this->outputsMutator)) {
            $this->outputs($this->outputsMutator->done());
        }

        return $this->transaction;
    }

    /**
     * @param array $array
     * @return $this
     */
    private function replace(array $array = [])
    {
        $this->transaction = new Transaction(
            isset($array['version']) ? $array['version'] : $this->transaction->getVersion(),
            isset($array['inputs']) ? $array['inputs'] : $this->transaction->getInputs(),
            isset($array['outputs']) ? $array['outputs'] : $this->transaction->getOutputs(),
            isset($array['nLockTime']) ? $array['nLockTime'] : $this->transaction->getLockTime()
        );

        return $this;
    }

    /**
     * @param int $nVersion
     * @return $this
     */
    public function version($nVersion)
    {
        return $this->replace(array('version' => $nVersion));
    }

    /**
     * @param TransactionInputCollection $inputCollection
     * @return $this
     */
    public function inputs(TransactionInputCollection $inputCollection)
    {
        return $this->replace(array('inputs' => $inputCollection));
    }

    /**
     * @param TransactionOutputCollection $outputCollection
     * @return $this
     */
    public function outputs(TransactionOutputCollection $outputCollection)
    {
        return $this->replace(array('outputs' => $outputCollection));
    }

    /**
     * @param int $locktime
     * @return $this
     */
    public function locktime($locktime)
    {
        return $this->replace(array('nLockTime' => $locktime));
    }
}