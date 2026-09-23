//////// USUARIO ////////

async function logar(email, senha) {

  try {

    const response = await fetch(
      `http://localhost/projeto-chamados/api/index.php?action=usuario.logar`,
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ email, senha }),
      }
    );

    if (!response.ok) {
      throw new Error("Credenciais inválidas");
    }

    const data = await response.json();

    console.log("Login realizado:", data);

    if (data.token) {
      localStorage.setItem("token", data.token);
    }

    document.getElementById("user-nome").textContent = data.nome || data.name;
    document.getElementById("user-email").textContent = data.email;

    document.getElementById("painel").classList.remove("d-none");
    bootstrap.Modal.getInstance(document.getElementById("login-model")).hide();

    return data;

  } catch (error) {

    document.getElementById("login-error").textContent = error.message;

  }

  document.getElementById("login-form").addEventListener("submit", (e) => {
    e.preventDefault();
    const email = document.getElementById("login-email").value;
    const senha = document.getElementById("login-password").value;
    logar(email, senha);
  });

}


async function buscarUsuarios() {

  try {

    const response = await fetch(
      `http://localhost/projeto-chamados/api/index.php?action=usuario.listarUsuarios`
    );

    if (!response.ok) {
      throw new Error(`Erro HTTP: ${response.status}`);
    }

    const data = await response.json();

    console.log(data);

    return data;

  } catch (error) {

    console.error("Falha na requisição:", error);

  }

}


async function buscarUsuarioId(id) {

  try {

    const response = await fetch(
      `http://localhost/projeto-chamados/api/index.php?action=usuario.buscarUsuarioPorId?id=${id}`
    );

    if (!response.ok) {
      throw new Error(`Erro HTTP: ${response.status}`);
    }

    const data = await response.json();

    console.log(data);

    return data;

  } catch (error) {

    console.error("Falha na requisição:", error);

  }

}


async function criarUsuario(dados) {

  try {

    const response = await fetch(
      `http://localhost/projeto-chamados/api/index.php?action=usuario.cadastrarUsuario`,
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(dados),
      }
    );

    if (!response.ok) {
      throw new Error("Erro ao criar usuário.");
    }

    const data = await response.json();

    console.log("Usuário criado com sucesso:", data);

    return data;

  } catch (error) {

    console.error("Erro:", error);

  }

}


async function atualizarUsuario(dados, id) {

  try {

    const response = await fetch(
      `http://localhost/projeto-chamados/api/index.php?action=usuario.atualizarUsuarios?id=${id}`,
      {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(dados),
      }
    );

    if (!response.ok) {
      throw new Error("Erro ao atualizar as informações do usuário");
    }

    const data = await response.json();

    console.log(data);

    return data;

  } catch (error) {

    console.error("Erro:", error);

  }

}


async function logout() {

  try {

    const token = localStorage.getItem("token");

    if (token) {

      await fetch(
        `http://localhost/projeto-chamados/api/index.php?action=logout`,
        {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "Authorization": `Bearer ${token}`,
          },
        }
      );

    }

  } catch (error) {

    console.error("Erro no logout:", error);

  } finally {

    localStorage.removeItem("token");

    window.location.href = "/login";

  }

}


async function deletarUsuario(id) {

  try {

    const response = await fetch(
      `http://localhost/projeto-chamados/api/index.php?action=deletarUsuario?id=${id}`,
      {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
        },
      }
    );

    if (!response.ok) {
      throw new Error("Erro ao deletar usuário");
    }

    const data = await response.json();

    console.log(data);

    return data;

  } catch (error) {

    console.error("Erro:", error);

  }

}


//////// FUNCIONARIO ////////

async function buscarFuncionarios() {

  try {

    const response = await fetch(
      "http://localhost/projeto-chamados/api/index.php?action=funcionarios.listarFuncionarios"
    );

    if (!response.ok) {
      throw new Error("Erro ao buscar funcionários");
    }

    const data = await response.json();

    console.log(data);

    return data;

  } catch (error) {

    console.error("Erro:", error);

  }

}


async function buscarFuncionarioId(id) {

  try {

    const response = await fetch(
      `http://localhost/projeto-chamados/api/index.php?action=funcionarios.buscarFuncionarioPorId?id=${id}`
    );

    if (!response.ok) {
      throw new Error("Erro ao achar o funcionário");
    }

    const data = await response.json();

    console.log(data);

    return data;

  } catch (error) {

    console.error("Erro:", error);

  }

}


async function criarFuncionario(dados) {

  try {

    const response = await fetch(
      `http://localhost/projeto-chamados/api/index.php?action=funcionarios.adicionarFuncionario`,
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(dados),
      }
    );

    if (!response.ok) {
      throw new Error("Erro ao cadastrar o funcionário");
    }

    const data = await response.json();

    console.log(data);

    return data;

  } catch (error) {

    console.error("Erro:", error);

  }

}


async function atualizarFuncionario(dados, id) {

  try {

    const response = await fetch(
      `http://localhost/projeto-chamados/api/index.php?action=funcionarios.atualizarFuncionario?id=${id}`,
      {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(dados),
      }
    );

    if (!response.ok) {
      throw new Error("Erro ao atualizar o funcionário");
    }

    const data = await response.json();

    console.log(data);

    return data;

  } catch (error) {

    console.error("Erro:", error);

  }

}


async function deletarFuncionario(id) {

  try {

    const response = await fetch(
      `http://localhost/projeto-chamados/api/index.php?action=funcionarios.deletarFuncionario?id=${id}`,
      {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
        },
      }
    );

    if (!response.ok) {
      throw new Error("Erro ao deletar o funcionário");
    }

    const data = await response.json();

    console.log(data);

    return data;

  } catch (error) {

    console.error("Erro:", error);

  }

}


//////// CATEGORIA ////////

async function buscarCategorias() {

  try {

    const response = await fetch(
      "http://localhost/projeto-chamados/api/index.php?action=categoria.listarCategorias"
    );

    if (!response.ok) {
      throw new Error("Erro ao buscar categorias");
    }

    const data = await response.json();

    console.log(data);

    return data;

  } catch (error) {

    console.error("Erro:", error);

  }

}


async function criarCategoria(dados) {

  try {

    const response = await fetch(
      `http://localhost/projeto-chamados/api/index.php?action=categoria.adicionarCategoria`,
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(dados),
      }
    );

    if (!response.ok) {
      throw new Error("Erro ao cadastrar a categoria");
    }

    const data = await response.json();

    console.log(data);

    return data;

  } catch (error) {

    console.error("Erro:", error);

  }

}


async function atualizarCategoria(dados, id) {

  try {

    const response = await fetch(
      `http://localhost/projeto-chamados/api/index.php?action=categoria.atualizarCategoria?id=${id}`,
      {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(dados),
      }
    );

    if (!response.ok) {
      throw new Error("Erro ao atualizar a categoria");
    }

    const data = await response.json();

    console.log(data);

    return data;

  } catch (error) {

    console.error("Erro:", error);

  }

}


async function deletarCategoria(id) {

  try {

    const response = await fetch(
      `http://localhost/projeto-chamados/api/index.php?action=categoria.deletarCategoria?id=${id}`,
      {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
        },
      }
    );

    if (!response.ok) {
      throw new Error("Erro ao deletar a categoria");
    }

    const data = await response.json();

    console.log(data);

    return data;

  } catch (error) {

    console.error("Erro:", error);

  }

}


//////// CHAMADOS ////////

async function buscarChamados() {

  try {

    const response = await fetch(
      "http://localhost/projeto-chamados/api/index.php?action=chamados.listarChamados"
    );

    if (!response.ok) {
      throw new Error("Erro ao buscar chamados");
    }

    const data = await response.json();

    console.log(data);

    return data;

  } catch (error) {

    console.error("Erro:", error);

  }

}


async function buscarChamadoId(id) {

  try {

    const response = await fetch(
      `http://localhost/projeto-chamados/api/index.php?action=chamados.buscarChamadoPorId?id=${id}`
    );

    if (!response.ok) {
      throw new Error("Erro ao achar o chamado");
    }

    const data = await response.json();

    console.log(data);

    return data;

  } catch (error) {

    console.error("Erro:", error);

  }

}


async function criarChamado(dados) {

  try {

    const response = await fetch(
      `http://localhost/projeto-chamados/api/index.php?action=chamados.novoChamado`,
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(dados),
      }
    );

    if (!response.ok) {
      throw new Error("Erro ao abrir novo chamado");
    }

    const data = await response.json();

    console.log(data);

    return data;

  } catch (error) {

    console.error("Erro:", error);

  }

}


async function atualizarChamado(dados, id) {

  try {

    const response = await fetch(
      `http://localhost/projeto-chamados/api/index.php?action=chamados.atualizarChamado?id=${id}`,
      {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(dados),
      }
    );

    if (!response.ok) {
      throw new Error("Erro ao atualizar o chamado");
    }

    const data = await response.json();

    console.log(data);

    return data;

  } catch (error) {

    console.error("Erro:", error);

  }

}


async function deletarChamado(id) {

  try {

    const response = await fetch(
      `http://localhost/projeto-chamados/api/index.php?action=chamados.deletarChamado?id=${id}`,
      {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
        },
      }
    );

    if (!response.ok) {
      throw new Error("Erro ao deletar o chamado");
    }

    const data = await response.json();

    console.log(data);

    return data;

  } catch (error) {

    console.error("Erro:", error);

  }

}


//////// MENSAGENS ////////

async function buscarMensagens() {

  try {

    const response = await fetch(
      "http://localhost/projeto-chamados/api/index.php?action=chamados.listarMensagens"
    );

    if (!response.ok) {
      throw new Error("Erro ao buscar mensagens");
    }

    const data = await response.json();

    console.log(data);

    return data;

  } catch (error) {

    console.error("Erro:", error);

  }

}


async function criarMensagem(dados) {

  try {

    const response = await fetch(
      `http://localhost/projeto-chamados/api/index.php?action=mensagens.adicionarMensagem`,
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(dados),
      }
    );

    if (!response.ok) {
      throw new Error("Erro ao mandar a mensagem");
    }

    const data = await response.json();

    console.log(data);

    return data;

  } catch (error) {

    console.error("Erro:", error);

  }

}


async function buscarMensagemId(id) {

  try {

    const response = await fetch(
      `http://localhost/projeto-chamados/api/index.php?action=mensagens.buscarMensagemPorId?id=${id}`
    );

    if (!response.ok) {
      throw new Error("Erro ao achar a mensagem");
    }

    const data = await response.json();

    console.log(data);

    return data;

  } catch (error) {

    console.error("Erro:", error);

  }

}


async function deletarMensagem(id) {

  try {

    const response = await fetch(
      `http://localhost/projeto-chamados/api/index.php?action=mensagens.deletarMensagem?id=${id}`,
      {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
        },
      }
    );

    if (!response.ok) {
      throw new Error("Erro ao apagar a mensagem");
    }

    const data = await response.json();

    console.log(data);

    return data;

  } catch (error) {

    console.error("Erro:", error);

  }

}

