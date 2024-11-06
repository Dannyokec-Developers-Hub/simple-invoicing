let itemIndex = 1;

function addItem() {
    const itemRow = document.createElement('div');
    itemRow.className = 'itemRow';
    itemRow.innerHTML = `
        <input type="text" name="items[${itemIndex}][name]" placeholder="Item Name" required>
        <input type="number" name="items[${itemIndex}][qty]" placeholder="Quantity" required>
        <input type="number" name="items[${itemIndex}][price]" placeholder="Price" required>
        <button type="button" onclick="removeItem(this)">-</button>
    `;
    document.getElementById('itemList').appendChild(itemRow);
    itemIndex++;
}

function removeItem(button) {
    button.parentElement.remove();
}
