
<?php
require 'vendor/autoload.php';
require_once '../modelo/funciones.php';

use PHPUnit\Framework\TestCase;

class UsuariosTest extends TestCase {

    protected function setUp(): void {
        // Asegura que los archivos usados en las pruebas estén limpios antes de empezar
        file_put_contents("usuarios.ini", "");
        file_put_contents("registro.log", "");
        @array_map('unlink', glob("usuarios/*/*")); // Elimina publicaciones de usuarios
        @array_map('rmdir', glob("usuarios/*", GLOB_ONLYDIR)); // Elimina carpetas de usuarios
        @rmdir("usuarios");
    }

    protected function tearDown(): void {
        // Limpia archivos y carpetas después de cada prueba
        $this->setUp();
    }

    public function testExisteUsuarioNoExiste() {
        // Intenta verificar un usuario que no existe
        $resultado = existe("usuario_inexistente");
        $this->assertNull($resultado, "El usuario no debería existir.");
    }

    public function testExisteUsuarioExiste() {
        // Guarda un usuario en el archivo
        grabar("usuario_test", "clave123");
        // Verifica que el usuario existe
        $resultado = existe("usuario_test");
        $this->assertEquals("clave123", $resultado, "La contraseña no coincide.");
    }

    public function testRegistrarUsuarioExistente() {
        // Registra un usuario existente
        grabar("usuario_test", "clave123");
        $resultado = registrar("usuario_test", "otra_clave");
        $this->assertFalse($resultado, "El registro de un usuario existente no debería ser exitoso.");
    }

    public function testAccederUsuarioValido() {
        // Registra y verifica acceso de un usuario
        grabar("usuario_test", "clave123");
        $resultado = acceder("usuario_test", "clave123");
        $this->assertTrue($resultado, "El acceso debería ser válido para credenciales correctas.");
    }

    public function testAccederUsuarioClaveIncorrecta() {
        // Registra un usuario y verifica acceso con clave incorrecta
        grabar("usuario_test", "clave123");
        $resultado = acceder("usuario_test", "clave_incorrecta");
        $this->assertFalse($resultado, "El acceso debería fallar para una clave incorrecta.");
    }

    public function testAccederUsuarioNoExistente() {
        // Intenta acceder con un usuario que no existe
        $resultado = acceder("usuario_inexistente", "clave123");
        $this->assertFalse($resultado, "El acceso no debería ser válido para un usuario inexistente.");
    }
}
