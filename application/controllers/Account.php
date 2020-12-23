<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends CI_Controller {

	public $response = array(
		"status"	=> "200",
		"message"	=> "Requisição processada",
		"class"		=> "success",
		"data"		=> NULL
	);

	function __construct() {
		parent::__construct();
		$_POST = json_decode(file_get_contents("php://input"), TRUE); 
	}

	public function signin()
	{
		if($this->input->post())
		{
			$this->form_validation->set_rules('email', 'E-mail', 'required|valid_email');
			$this->form_validation->set_rules('password', 'Senha', 'required');

			if($this->form_validation->run() == TRUE)
			{
				$this->load->model("account_model");

				$where = array(
					"email"		=> $this->input->post("email"),
				);
				$user = $this->account_model->get($where);

				if($user)
				{
					$user = $user[0];
					if(password_verify($this->input->post("password"), $user["password"])) {
						$user['logado'] = TRUE;
						unset($user['password']);

						$this->session->set_userdata($user);
						$this->response = array(
							"status"	=> "200",
							"message"	=> "Você foi autenticado com sucesso!",
							"class"		=> "success",
							"data"		=> $user
						);
					}
					else
					{
						$this->response = array(
							"status"	=> "500",
							"message"	=> "Dados inválidos",
							"class"		=> "danger",
							"data"		=> NULL
						);
					}
				}
				else
				{
					$this->response = array(
						"status"	=> "500",
						"message"	=> "Dados inválidos",
						"class"		=> "danger",
						"data"		=> NULL
					);
				}
			}
			else
			{
				$this->response = array(
					"status"	=> "500",
					"message"	=> "Falha na validação do formulário!<br>" . validation_errors(),
					"class"		=> "danger",
					"data"		=> NULL
				);
			}
		}
		else
		{
			$this->response = array(
				"status"	=> "500",
				"message"	=> "Requisição inválida",
				"class"		=> "danger",
				"data"		=> NULL
			);
		}
		$this->output();
	}

	public function signup()
	{
		if($this->input->post())
		{
			$this->form_validation->set_rules('email', 'E-mail', 'required|valid_email|is_unique[users.email]');
			$this->form_validation->set_rules('password', 'Senha', 'required');
			$this->form_validation->set_rules('password_confirm', 'Confirmar Senha', 'required|matches[password]');

			if($this->form_validation->run() == TRUE)
			{
				$this->load->model("account_model");

				$user = array(
					"email"		=> $this->input->post("email"),
					"password"	=> password_hash($this->input->post("password"), PASSWORD_DEFAULT)
				);
				$inserted_id = $this->account_model->create($user);

				if($inserted_id)
				{
                    $user['id'] = $inserted_id;
                    $user['logado'] = TRUE;
                    unset($user['password']);

					$this->session->set_userdata($usuario);
					$this->response = array(
						"status"	=> "200",
						"message"	=> "Cadastro realizado com sucesso!",
						"class"		=> "success",
						"data"		=> $user
					);
				}
				else
				{
					$this->response = array(
						"status"	=> "500",
						"message"	=> "Houve um problema ao realizar o seu cadastro, tente novamente mais tarde.",
						"class"		=> "danger",
						"data"		=> NULL
					);
				}
			}
			else
			{
				$this->response = array(
					"status"	=> "500",
					"message"	=> "Falha na validação do formulário!<br>" . validation_errors(),
					"class"		=> "danger",
					"data"		=> NULL
				);
			}
		}
		else
		{
			$this->response = array(
				"status"	=> "500",
				"message"	=> "Requisição inválida",
				"class"		=> "danger",
				"data"		=> NULL
			);
		}
		$this->output();
	}

	public function logout()
	{
		$this->session->sess_destroy();
		$this->response = array(
			"status"	=> "200",
			"message"	=> "Sessão finalizada com sucesso!",
			"class"		=> "success",
			"data"		=> NULL
		);
		$this->output();
	}

	private function output()
	{
		$this->output
			->set_status_header($this->response["status"])
			->set_content_type('application/json', 'utf-8')
			->set_output(json_encode($this->response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
			->_display();
		exit;
	}
}
