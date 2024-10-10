<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Orders</title>
        <link rel="stylesheet" href="{{ asset('css/nexaris3.css')}}">

    </head>
    <body>
        <div id="app-page">
            <div id="app-content">
               <div class="page-layout">
                <div class="page-layout-content">

                  <div class="container-fluid">

                    <div class="page-layout-header">
                      <h1 class="page-layout-title">Page header</h1>
                      <p class="page-layout-subtitle">Page layout subtitle</p>
                    </div>

                    <div class="page-layout-section">

                      <div class="card">
                        {{-- <div class="card-header">
                          <div class="card-heading">
                            <h2 class="card-title">Card title</h2>
                            <a href="#1">Link</a>
                          </div>
                        </div>
                        <div class="card-section">
                          <h3 class="card-subtitle">Card Subtitle</h3>
                          <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Saepe, nesciunt.</p>
                        </div> --}}

                        <div class="card-section">
                            <div class="row">
                                <div class="col-lg-4">
                                  <div class="form-select">
                                      <label for="financial-status-filter">Filter by Financial Status:</label>
                                      <div class="select-holder">
                                      <select id="financial-status-filter">
                                          <option value="" hidden disabled selected>All</option>
                                          <option value="paid">Paid</option>
                                          <option value="pending">Pending</option>
                                          <option value="failed">Failed</option>
                                      </select>
                                      </div>
                                      {{-- <p class="error-message">Error message</p>
                                      <p class="help-text">Help text goes here</p> --}}
                                  </div>
                                  </div>
                                  <div class="col-lg-4">
                                  </div>
                                  <div class="col-lg-4">

                                  <button class="btn btn-secondary btn-icon">
                                    <span class="btn-icon-holder">
                                      <svg viewBox="0 0 20 20" class="Icon_Icon__Dm3QW" style="width: 20px; height: 20px;"><path d="M10 20c-5.514 0-10-4.486-10-10s4.486-10 10-10 10 4.486 10 10-4.486 10-10 10zm-1-8.414-1.293-1.293a1 1 0 0 0-1.414 1.414l3 3a.998.998 0 0 0 1.414 0l3-3a1 1 0 0 0-1.414-1.414l-1.293 1.293v-5.586a1 1 0 0 0-2 0v5.586z"></path></svg>
                                    </span>Import
                                  </button>
                                  </div>
                              </div>
                            <div class="card-section">

                            </div>
                            <table class="index-table">
                                <thead>
                                    <tr>
                                        <th>Customer Name</th>
                                        <th>Customer Email</th>
                                        <th>Total Price</th>
                                        <th>Financial Status</th>
                                        <th>Fulfillment Status</th>
                                    </tr>
                                </thead>
                                <tbody id="orders-table-body">
                                    <!-- AJAX will load data here -->
                                </tbody>
                            </table>



                        </div>
                        <div class="card-footer">


                            <!-- Pagination Links -->
                            <div id="pagination-links">
                                <!-- AJAX will load pagination links here -->
                            </div>

                          {{-- <a href="#1" class="btn ">Dismiss</a>
                          <a href="#1" class="btn btn-primary">Confirm</a> --}}
                        </div>
                      </div>

                    </div>

                    <div class="page-layout-footer">

                    </div>

                  </div>

                </div>
              </div>
            </div>
          </div>
    </body>
</html>


<script>
document.addEventListener('DOMContentLoaded', function () {
    // Load initial data
    loadOrders();

    // Function to load orders with AJAX
    function loadOrders(page = 1, financial_status = '') {
        const url = new URL("{{ route('orders.get') }}");
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

      // Function to render only previous and next pagination links
      function renderPaginationLinks(links, $currentPage, $lastPage) {
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

    // Event delegation for pagination
    document.getElementById('pagination-links').addEventListener('click', function (e) {
        if (e.target.tagName === 'A' && !e.target.classList.contains('disabled')) {
            e.preventDefault();
            const pageUrl = e.target.href;

            const page = new URL(pageUrl).searchParams.get('page');
            loadOrders(page);
        }
    });


    // Filter orders by financial status
    document.getElementById('financial-status-filter').addEventListener('change', function () {
        const financial_status = this.value;
        loadOrders(1, financial_status); // Load the first page with the selected filter
    });
});
</script>
