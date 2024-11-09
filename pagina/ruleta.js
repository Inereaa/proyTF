/**
 * Inicia el evento principal cuando el DOM está completamente cargado.
 * Este script controla el comportamiento de una ruleta para mostrar premios aleatorios.
 */
document.addEventListener('DOMContentLoaded', () => {
    iniciarRuleta();
});

/**
 * Inicializa los elementos de la ruleta y el botón de giro,
 * y configura el evento de clic para el botón.
 */
function iniciarRuleta() {
    const ruleta = document.getElementById('ruleta');
    const botonGirar = document.getElementById('girar-ruleta');
    const resultado = document.getElementById('resultado');
    let girando = false;

    botonGirar.addEventListener('click', () => {
        if (!girando) {
            girarRuleta(ruleta, resultado, () => girando = false);
            girando = true;
        }
    });
}

/**
 * Gira la ruleta al azar y muestra el premio correspondiente.
 *
 * @param {HTMLElement} ruleta - Elemento HTML que representa la ruleta.
 * @param {HTMLElement} resultado - Elemento HTML donde se mostrará el resultado del premio.
 * @param {Function} callback - Función a ejecutar una vez que el giro haya terminado.
 */
function girarRuleta(ruleta, resultado, callback) {
    const grados = generarAnguloAleatorio();
    ruleta.style.transform = `rotate(${grados}deg)`;
    resultado.textContent = '';

    setTimeout(() => {
        const premio = calcularPremio(grados);
        resultado.textContent = `¡Has ganado ${premio}!`;
        callback();
    }, 5000);
}

/**
 * Genera un ángulo aleatorio para el giro de la ruleta, con un mínimo de dos vueltas completas.
 *
 * @returns {number} Un ángulo en grados para la rotación de la ruleta.
 */
function generarAnguloAleatorio() {
    return Math.floor(Math.random() * 360) + 720;
}

/**
 * Calcula el premio basado en el ángulo final de la ruleta.
 *
 * @param {number} grados - El ángulo final de rotación de la ruleta.
 * @returns {string} El premio correspondiente.
 */
function calcularPremio(grados) {
    const opciones = [
        '3€ para la ruleta',
        '2 tiradas más gratis',
        '0.50€ de depósito en tu cuenta',
        'Un bono de 4€',
        '1 tirada más',
        '2€ de depósito en tu cuenta'
    ];
    const indice = Math.floor((grados % 360) / 60);
    return opciones[indice];
}
