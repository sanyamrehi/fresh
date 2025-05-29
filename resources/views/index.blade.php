<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Product Blog Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    body {
      background-color: #f9f9f9;
    }
    .card {
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    }
    .card img {
      height: 250px;
      object-fit: cover;
    }
    .search-bar {
      max-width: 500px;
      margin: 0 auto;
    }
    .modal-body textarea,
    .modal-body input {
      background-color: #f1f1f1;
    }
  </style>
</head>
<body>

<div class="container my-5">
  <h1 class="text-center mb-4 fw-bold">🛒 Product Blog</h1>

  @if(session()->has('customer'))
    <div class="text-end mb-3">
      <a href="{{ url('customer/login') }}" class="btn btn-outline-primary">Customer Login</a>
    </div>
  @endif

  <div class="search-bar mb-4">
    <input type="text" id="searchInput" class="form-control shadow-sm" placeholder="🔍 Search by name, category, size, or price">
  </div>

  <div class="d-flex justify-content-end mb-3">
    <a href="{{ route('order.index') }}" class="btn btn-success shadow-sm">Purchase History</a>
  </div>

  <div class="row g-4" id="productGrid"></div>

  <nav>
    <ul class="pagination justify-content-center mt-4" id="paginationLinks"></ul>
  </nav>
</div>

<!-- Address Modal -->
<div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content shadow-lg">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Select Your Address</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="addressForm" action="{{ route('address.store') }}" method="POST">
          @csrf
          <div class="mb-3">
            <label for="addressSelect" class="form-label">Saved Addresses</label>
            <select class="form-select" id="addressSelect" name="address_id">
              <option value="">-- Select an Address --</option>
              @foreach($addresses as $address)
                <option value="{{ $address->id }}"
                        data-address="{{ $address->address }}"
                        data-city="{{ $address->city }}"
                        data-state="{{ $address->state }}"
                        data-pincode="{{ $address->pincode }}">
                  {{ $address->address }}
                </option>
              @endforeach
            </select>
            <input type="hidden" name="selected_address_id" id="selectedAddressIdInput">
            <small class="form-text text-muted">Selected Address ID: <span id="selectedAddressId">None</span></small>
          </div>

          <hr class="my-3">

          <div class="mb-3">
            <label for="address" class="form-label">New Address</label>
            <textarea class="form-control" id="address" name="address" rows="2"></textarea>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <input type="text" class="form-control" id="city" name="city" placeholder="City">
            </div>
            <div class="col-md-6 mb-3">
              <input type="text" class="form-control" id="state" name="state" placeholder="State">
            </div>
            <div class="col-12 mb-3">
              <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Pincode">
            </div>
          </div>

          <div class="modal-footer">
            <a id="nextButton" class="btn btn-primary w-100" disabled>Next</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- jQuery Script -->
<script>
let selectedProductId;

$(document).ready(function () {
  function fetchProducts(search = '', page = 1) {
    $.ajax({
      url: '{{ route('product.index') }}',
      type: 'GET',
      data: { search: search, page: page, per_page: 6 },
      success: function (response) {
        const productGrid = $('#productGrid');
        const paginationLinks = $('#paginationLinks');
        productGrid.empty();
        paginationLinks.empty();

        response.data.forEach((product) => {
          productGrid.append(`
            <div class="col-md-4">
              <div class="card h-100 shadow-sm border-0">
                <img src="/fresh_images/${product.image}" class="card-img-top rounded-top" alt="${product.name}">
                <div class="card-body d-flex flex-column">
                  <h5 class="card-title">${product.name}</h5>
                  <p class="card-text small mb-2">
                    <strong>Color:</strong> ${product.color}<br>
                    <strong>Size:</strong> ${product.sizes}<br>
                    <strong>Category:</strong> ${product.categories}<br>
                    <strong>Price:</strong> ₹${product.price}
                  </p>
                  <div class="mt-auto">
                    <button class="btn btn-warning btn-sm w-100 addToCartBtn" data-id="${product.id}">Add to Cart</button>
                  </div>
                </div>
              </div>
            </div>
          `);
        });

        for (let i = 1; i <= response.last_page; i++) {
          paginationLinks.append(`
            <li class="page-item ${i === response.current_page ? 'active' : ''}">
              <a class="page-link" href="#" data-page="${i}">${i}</a>
            </li>
          `);
        }
      }
    });
  }

  fetchProducts();

  $('#searchInput').on('keyup', function () {
    fetchProducts($(this).val());
  });

  $(document).on('click', '.page-link', function (e) {
    e.preventDefault();
    fetchProducts($('#searchInput').val(), $(this).data('page'));
  });

  $(document).on('click', '.addToCartBtn', function () {
    let isAuthenticated = {{ session()->has('customer') ? 'true' : 'false' }};
    if (!isAuthenticated) {
      alert("Please log in to continue.");
      window.location.href = "{{ url('customer/login') }}";
      return;
    }

    selectedProductId = $(this).data('id');
    $('#addressModal').modal('show');
  });

  $('#addressSelect').on('change', function () {
    let selected = $(this).find(':selected');
    $('#address').val(selected.data('address') || '');
    $('#city').val(selected.data('city') || '');
    $('#state').val(selected.data('state') || '');
    $('#pincode').val(selected.data('pincode') || '');
    $('#selectedAddressId').text(selected.val() || 'None');
    $('#selectedAddressIdInput').val(selected.val());
    validateAddressForm();
  });

  $('#address, #city, #state, #pincode').on('input', function () {
    $('#addressSelect').val('');
    $('#selectedAddressId').text('None');
    $('#selectedAddressIdInput').val('');
    validateAddressForm();
  });

  function validateAddressForm() {
    let hasExisting = $('#addressSelect').val() !== '';
    let newAddress = $('#address').val().trim() !== '' &&
                     $('#city').val().trim() !== '' &&
                     $('#state').val().trim() !== '' &&
                     /^[0-9]{6}$/.test($('#pincode').val().trim());

    $('#nextButton').prop('disabled', !(hasExisting || newAddress));
  }

  $('#nextButton').on('click', function (e) {
    e.preventDefault();
    let selectedAddressId = $('#addressSelect').val();
    let address = $('#address').val().trim();
    let city = $('#city').val().trim();
    let state = $('#state').val().trim();
    let pincode = $('#pincode').val().trim();

    if (!selectedAddressId && address && city && state && pincode) {
      $.ajax({
        url: "{{ route('address.store') }}",
        method: 'POST',
        data: {
          _token: "{{ csrf_token() }}",
          address: address,
          city: city,
          state: state,
          pincode: pincode
        },
        success: function () {
          alert("Address saved!");
          if (selectedProductId) {
            window.location.href = "{{ route('product.show', ':id') }}".replace(':id', selectedProductId);
          }
        },
        error: function (xhr) {
          let errors = xhr.responseJSON.errors;
          alert(Object.values(errors).join('\n'));
        }
      });
    } else if (selectedProductId && selectedAddressId) {
      window.location.href = "{{ route('product.show', ':id') }}".replace(':id', selectedProductId) + "?address_id=" + selectedAddressId;
    } else {
      alert("Please enter or select a valid address.");
    }
  });

  validateAddressForm();
});
</script>
</body>
</html>
