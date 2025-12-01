<?php

class AdminDashboard
{
    private $select;

    public function __construct(string $escola)
    {
        require_once __DIR__ . '/../../ss/config/connect.php';
        require_once __DIR__ . '/../../ss/models/model.select.php';

        $this->select = new select($escola);
    }

    public function getCursosChartData(): array
    {
        return $this->select->countAlunosPorCurso();
    }

    public function getUsuariosEscola(): array
    {
        return $this->select->select_usuarios();
    }

    public function getQuickStats(): array
    {
        return [
            'totalAlunos'    => $this->select->countTotalAlunos(),
            'totalPublicos'  => $this->select->countTotalPublicos(),
            'totalPrivados'  => $this->select->countTotalPrivados(),
            'totalPCDs'      => $this->select->countTotalPCDs(),
        ];
    }

    public function getCandidatos(): array
    {
        return $this->select->select_candidatos();
    }
}
