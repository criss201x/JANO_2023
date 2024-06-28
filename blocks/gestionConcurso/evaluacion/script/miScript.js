function calcularTotal(item, criterio) {
    let total = 0;
    const divs = document.querySelectorAll('.number'+item);
    divs.forEach(div => {
      const input = div.querySelector('input');
      const value = parseFloat(input.value) || 0;
      total += value;
    });
    const divTotal = document.querySelector('.total'+item);
    const inputTotal = divTotal.querySelector('input');
    inputTotal.value = total;

    let subtotal = 0;
    const divsSubtotalCriterio = document.querySelectorAll('.subtotal'+criterio);
    divsSubtotalCriterio.forEach(divSubtotal => {
      const input = divSubtotal.querySelector('input');
      const value = parseFloat(input.value) || 0;
      subtotal += value;
    });
    document.getElementById('totalCriterio' + criterio).innerText = subtotal;

    const divSubtotal = document.querySelector('.subtotalCriterio'+criterio);
    const inputSubtotal = divSubtotal.querySelector('input');
    inputSubtotal.value = subtotal;
}
