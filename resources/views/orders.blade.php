<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Orders</title>
    <link rel="stylesheet" href="{{ asset('css/nexaris3.css') }}">

</head>

<body>
    <div id="app-page">
        <div id="app-content">
            <div class="page-layout">
                <div class="page-layout-content">

                    <div class="container-fluid">

                        <div class="page-layout-header">
                            <h1 class="page-layout-title">Shopify orders reader</h1>
                            <p class="page-layout-subtitle">On this page you can import and read all shopify orders with total price more than 100</p>
                            <p class="page-layout-subtitle">Page has a filter by financial status and works entirely with Ajax without reloading</p>
                            <p class="page-layout-subtitle">It uses custom styles from Nexaris v3</p>
                        </div>

                        <div class="page-layout-section">

                            <div class="card">
                                <div class="card-section">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-select">
                                                <label for="financial-status-filter">Filter by Financial Status:</label>
                                                <div class="select-holder">
                                                    <select id="financial-status-filter">
                                                        <option value="" selected>All</option>
                                                        @php
                                                            $financial_statuses = ['paid','pending','authorized','partially_paid','partially_refunded','refunded','voided','expired'];
                                                        @endphp
                                                        @foreach ($financial_statuses as $status)
                                                            <option value="{{strtolower($status)}}">{{ucfirst($status)}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                {{-- <p class="error-message">Error message</p>
                                      <p class="help-text">Help text goes here</p> --}}
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                        </div>
                                        <div class="col-lg-4 text-right">
                                            <button class="btn btn-secondary btn-icon" id="import-button">
                                                <span class="btn-icon-holder">
                                                    <svg viewBox="0 0 20 20" class="Icon_Icon__Dm3QW"
                                                        style="width: 20px; height: 20px;">
                                                        <path
                                                            d="M10 20c-5.514 0-10-4.486-10-10s4.486-10 10-10 10 4.486 10 10-4.486 10-10 10zm-1-8.414-1.293-1.293a1 1 0 0 0-1.414 1.414l3 3a.998.998 0 0 0 1.414 0l3-3a1 1 0 0 0-1.414-1.414l-1.293 1.293v-5.586a1 1 0 0 0-2 0v5.586z">
                                                        </path>
                                                    </svg>
                                                </span>Import data
                                            </button>
                                        </div>
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
    <script type="module" src="{{ asset('js/orders/index.js') }}"></script>
</body>
</html>
