<?php
class Linha {
	private $numero;
	private $nome;
	private $horariosSaidaBairroUteis;
	private $horariosSaidaCentroUteis;
	private $horariosSaidaBairroSabado;
	private $horariosSaidaCentroSabado;
	private $horariosSaidaBairroDomingo;
	private $horariosSaidaCentroDomingo;
	private $obs;

	function __construct(){
		
	}
	
	function __construct($numero, 
						 $nome, 
						 $horariosSaidaBairroUteis, 
						 $horariosSaidaCentroUteis, 
						 $horariosSaidaBairroSabado, 
						 $horariosSaidaCentroSabado,
						 $horariosSaidaBairroDomingo,
						 $horariosSaidaCentroDomingo,
						 $obs){

		$this->numero
		$this->nome
		$this->horariosSaidaBairroUteis = $horariosSaidaBairroUteis;
		$this->horariosSaidaCentroUteis = $horariosSaidaCentroUteis;
		$this->horariosSaidaBairroSabado = $horariosSaidaBairroSabado;
		$this->horariosSaidaCentroSabado = $horariosSaidaCentroSabado
		$this->horariosSaidaBairroDomingo = $horariosSaidaBairroDomingo;
		$this->horariosSaidaCentroDomingo = $horariosSaidaCentroDomingo;

	}
}

?>