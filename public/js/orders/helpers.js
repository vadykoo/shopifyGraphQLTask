export function loadOrders(page = 1, financial_status = '') {
    const url = new URL("/api/orders", window.location.origin);
    url.searchParams.append('page', page);
    url.searchParams.append('financial_status', financial_status);

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const ordersTableBody = document.getElementById('orders-table-body');
            ordersTableBody.innerHTML = '';

            // Append order data into the table
            data.data.forEach(order => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${order.customer.first_name || ''} ${order.customer.last_name || ''}</td>
                    <td>${order.customer.email || 'N/A'}</td>
                    <td>${order.total_price}</td>
                    <td>${order.financial_status}</td>
                    <td>${order.fulfillment_status}</td>
                `;
                ordersTableBody.appendChild(row);
            });

            // Update pagination links
            renderPaginationLinks(data.meta.links, data.meta.current_page, data.meta.last_page);
        })
        .catch(error => console.error('Error fetching orders:', error));
}

export function renderPaginationLinks(links, $currentPage, $lastPage) {
    const paginationLinksContainer = document.getElementById('pagination-links');
    paginationLinksContainer.innerHTML = '';

    const prevLink = links.find(link => link.label === '&laquo; Previous');
    const nextLink = links.find(link => link.label === 'Next &raquo;');

    const div = document.createElement('div');
    div.classList.add('index-table-footer');
    console.log(links);
    const resultText = document.createElement('div');
    resultText.classList.add('table-result');
    resultText.innerText = `Showing ${$currentPage} of ${$lastPage} results`;
    div.appendChild(resultText);

    const buttonGroup = document.createElement('div');
    buttonGroup.classList.add('button-group');

    // Previous Button
    const prevButton = document.createElement('a');
    prevButton.href = prevLink.url || '#';
    prevButton.classList.add('btn', 'btn-icon', 'btn-no-label');
    prevButton.innerHTML = `
      <span class="btn-icon-holder">
          <svg viewBox="0 0 20 20" class="Icon_Icon__Dm3QW" style="width: 20px; height: 20px;">
              <path d="M12 16a.997.997 0 0 1-.707-.293l-5-5a.999.999 0 0 1 0-1.414l5-5a.999.999 0 1 1 1.414 1.414l-4.293 4.293 4.293 4.293a.999.999 0 0 1-.707 1.707z"></path>
          </svg>
      </span>
      prev
  `;
    if (prevLink.url === null) {
        prevButton.classList.add('disabled');
    }
    buttonGroup.appendChild(prevButton);

    // Next Button
    const nextButton = document.createElement('a');
    nextButton.href = nextLink.url || '#';
    nextButton.classList.add('btn', 'btn-icon', 'btn-no-label');
    nextButton.innerHTML = `
      <span class="btn-icon-holder">
          <svg viewBox="0 0 20 20" class="Icon_Icon__Dm3QW" style="width: 20px; height: 20px;">
              <path d="M8 16a.999.999 0 0 1-.707-1.707l4.293-4.293-4.293-4.293a.999.999 0 1 1 1.414-1.414l5 5a.999.999 0 0 1 0 1.414l-5 5a.997.997 0 0 1-.707.293z"></path>
          </svg>
      </span>
      next
  `;
    if (nextLink.url === null) {
        nextButton.classList.add('disabled');
    }
    buttonGroup.appendChild(nextButton);

    div.appendChild(buttonGroup);
    paginationLinksContainer.appendChild(div);
}

export function importOrders() {
    const importUrl = "/api/import-orders";

    fetch(importUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({})
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Import successful:', data);
            alert(data.message);
            loadOrders();
        })
        .catch(error => {
            console.error('Error importing orders:', error);
            alert(error.message);
        });
}
