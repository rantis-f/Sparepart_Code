<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login - Aplikasi RO</title>
  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Font Google -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <style>
    body {
        font-family: 'Inter', sans-serif;
        background-image: url('/images/login-bg.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .container {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        max-width: 400px;
        width: 100%;
        overflow: hidden;
    }
    .header {
        background: #081b8d;
        color: white;
        text-align: center;
        padding: 20px;
        border-bottom: 1px solid #00A0E3;
    }
    .btn-primary {
        background: #081b8d;
        hover:bg: #001440;
        color: white;
        border: none;
        padding: 12px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: bold;
        transition: all 0.2s;
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center py-12 px-4">

  <!-- Container Utama -->
  <div class="w-full max-w-md bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-200">

    <!-- Header -->
    <div class="bg-[#081b8d] text-white py-3 px-8 text-center">
      <div class="flex flex-col items-center justify-center space-y-1">
        <img src="{{ asset('images/logo-pgn.png') }}" alt="PGN Logo" class="h-16">
        <div class="text-center">
          <p class="text-xs text-[#ffff]">Sistem Manajemen Sparepart</p>
        </div>
      </div>
    </div>

    <!-- Form Login -->
    <div class="p-8 space-y-6">
      <h2 class="text-xl font-semibold text-gray-800 text-center">Masuk ke Akun Anda</h2>

      <!-- Alert Error -->
      @if ($errors->any())
        <div class="bg-red-50 text-red-600 text-sm p-3 rounded-lg border border-red-200">
          <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <!-- Form -->
      <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="space-y-4">
          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input
              id="email"
              type="email"
              name="email"
              value="{{ old('email') }}"
              required
              autofocus
              class="w-full px-4 py-3 rounded-lg input-field shadow-sm transition"
              placeholder="contoh@pgn.co.id"
            />
          </div>

          <!-- Password -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <div class="relative">
              <input
                id="password"
                type="password"
                name="password"
                required
                class="w-full px-4 py-3 rounded-lg input-field shadow-sm transition pr-10"
                placeholder="••••••••"
              />
              <button
                type="button"
                id="togglePassword"
                class="absolute inset-y-0 right-3 flex items-center text-gray-500 hover:text-gray-700"
              >
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>

          <!-- Remember Me -->
          <div class="flex items-center justify-between">
            <label class="flex items-center">
              <input type="checkbox" name="remember" class="rounded border-gray-300 text-[#002060] focus:ring-[#00A0E3]">
              <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
            </label>
          </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full py-3 mt-6 rounded-lg btn-primary font-semibold text-lg shadow-md transform transition hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#00A0E3]">
          MASUK
        </button>
      </form>
    </div>

    <!-- Footer -->
    <div class="bg-gray-50 px-8 py-4 text-center border-t border-gray-100">
      <p class="text-xs text-gray-500">
        &copy; {{ date('Y') }} PT PGN COM. Hak Cipta Dilindungi.
      </p>
    </div>
  </div>

  <!-- Script Toggle Password -->
  <script>
    document.getElementById("togglePassword").addEventListener("click", function () {
      const input = document.getElementById("password");
      const icon = this.querySelector("i");
      if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("bi-eye");
        icon.classList.add("bi-eye-slash");
      } else {
        input.type = "password";
        icon.classList.remove("bi-eye-slash");
        icon.classList.add("bi-eye");
      }
    });
  </script>

</body>
</html>
