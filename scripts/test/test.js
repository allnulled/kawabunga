// test.js
const URL_OBJETIVO = "http://127.0.0.1/kawabunga/index.php";
const assert = require('assert');
const http = require('http');
const { URL } = require('url');

const peticion = function(urlCompleta, datosEnviados) {
  return new Promise((resolve, reject) => {
    const urlParseada = new URL(urlCompleta);

    // Configuración de la solicitud
    const opciones = {
      hostname: urlParseada.hostname,
      port: urlParseada.port || 80,
      path: urlParseada.pathname,
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
    };

    // Crear el objeto de solicitud
    const solicitud = http.request(opciones, (respuesta) => {
      let datos = '';

      // Recibir datos de la respuesta
      respuesta.on('data', (chunk) => {
        datos += chunk;
      });

      // Manejar el evento 'end' cuando la respuesta está completa
      respuesta.on('end', () => {
        try {
          resolve(JSON.parse(datos));
        } catch (error) {
          resolve(datos);
        }
      });
    });

    // Manejar errores de la solicitud
    solicitud.on('error', (error) => {
      reject(error);
    });

    // Enviar datos en el cuerpo de la solicitud (en este caso, un objeto JSON)
    solicitud.write(JSON.stringify(datosEnviados));

    // Finalizar la solicitud
    solicitud.end();
  });
};

const comprobar_que = function(condicion) {
  assert(condicion);
};

const comprobar_error_de_peticion = function(response) {
  console.log(response);
  if(typeof response === "string") {
    throw response;
  }
  const es_error = Object.keys(response).indexOf("error")  !== -1;
  if(es_error) {
    console.log(response);
    throw response.error;
  }
};

describe('Tiki tests', function () {

  const options = {};
  describe('Sistema de autentificación', async function () {
    it('controlador «registrar_usuario»', async function () {
      const response = await peticion(URL_OBJETIVO, {
        operacion: "registrar_usuario",
        email: "noadmin@noadmin.com",
        nombre: "noadmin",
        contrasenya: "noadmin"
      });
      comprobar_error_de_peticion(response);
      options.token_confirmacion = response.token_confirmacion;
    });
    it('controlador «confirmar_cuenta»', async function () {
      const response = await peticion(URL_OBJETIVO, {
        operacion: "confirmar_cuenta",
        email: "noadmin@noadmin.com",
        token_confirmacion: options.token_confirmacion
      });
      comprobar_error_de_peticion(response);
    });
    it('controlador «iniciar_sesion»', async function () {
      const response = await peticion(URL_OBJETIVO, {
        operacion: "iniciar_sesion",
        email: "noadmin@noadmin.com",
        contrasenya: "noadmin"
      });
      comprobar_error_de_peticion(response);
      options.token_sesion = response.token_sesion;
    });
    it('controlador «cerrar_sesion»', async function () {
      const response = await peticion(URL_OBJETIVO, {
        operacion: "cerrar_sesion",
        token: options.token_sesion
      });
      comprobar_error_de_peticion(response);
    });
    it('controlador «olvido_credenciales»', async function () {
      const response = await peticion(URL_OBJETIVO, {
        operacion: "olvido_credenciales",
        email: "noadmin@noadmin.com"
      });
      comprobar_error_de_peticion(response);
      options.token_recuperacion = response.token_recuperacion;
    });
    it('controlador «recuperar_credenciales»', async function () {
      const response = await peticion(URL_OBJETIVO, {
        operacion: "recuperar_credenciales",
        email: "noadmin@noadmin.com",
        token: options.token_recuperacion,
        contrasenya: "noadmin1"
      });
      comprobar_error_de_peticion(response);
    });
    it('controlador «baja_del_sistema»', async function () {
      const response_login = await peticion(URL_OBJETIVO, {
        operacion: "iniciar_sesion",
        email: "noadmin@noadmin.com",
        contrasenya: "noadmin1"
      });
      comprobar_error_de_peticion(response_login);
      options.token_sesion = response_login.token_sesion;
      const response = await peticion(URL_OBJETIVO, {
        operacion: "baja_del_sistema",
        token: options.token_sesion
      });
      comprobar_error_de_peticion(response);
    });
  });
  describe('Sistema de datos', async function () {
    it('controlador «esquema»', async function () {
      const response = await peticion(URL_OBJETIVO, {
        operacion: "esquema"
      });
      comprobar_error_de_peticion(response);
    });
    it('controlador «insertar»', async function () {
      const response = await peticion(URL_OBJETIVO, {
        operacion: "insertar",
        tabla: "usuarios",
        valores: {
          nombre: "noadmin2",
          email: "noadmin2@noadmin2.com",
          contrasenya: "noadmin2"
        }
      });
      comprobar_error_de_peticion(response);
    });
    it('controlador «seleccionar» / test 1', async function () {
      const response = await peticion(URL_OBJETIVO, {
        operacion: "seleccionar",
        tabla: "usuarios"
      });
      comprobar_error_de_peticion(response);
      options.id_usuario =  response.filter(usuario => usuario.nombre === "noadmin2")[0].id;
    });
    it('controlador «actualizar»', async function () {
      const response = await peticion(URL_OBJETIVO, {
        operacion: "actualizar",
        tabla: "usuarios",
        id: options.id_usuario,
        valores: {
          nombre: "noadmin3",
          email: "noadmin3@noadmin3.com",
          contrasenya: "noadmin3"
        }
      });
      comprobar_error_de_peticion(response);
    });
    it('controlador «seleccionar» / test 2', async function () {
      const response = await peticion(URL_OBJETIVO, {
        operacion: "seleccionar",
        tabla: "usuarios"
      });
      comprobar_error_de_peticion(response);
    });
    it('controlador «eliminar»', async function () {
      const response = await peticion(URL_OBJETIVO, {
        operacion: "eliminar",
        tabla: "usuarios",
        id: options.id_usuario
      });
      comprobar_error_de_peticion(response);
    });
    it('controlador «seleccionar» / test 3', async function () {
      const response = await peticion(URL_OBJETIVO, {
        operacion: "seleccionar",
        tabla: "usuarios"
      });
      comprobar_error_de_peticion(response);
    });
  });
});
