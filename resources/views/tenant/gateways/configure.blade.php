<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
</head>

<body>
  <!-- resources/views/tenant/gateways/configure.blade.php -->

  <h1>hello</h1>
  {{-- <div class="container">
    <h2>Configure {{ $gatewayConfig['display_name'] }}</h2>

    <form action="{{ route('tenant.gateways.store', $gatewayName) }}" method="POST">
      @csrf

      @foreach($gatewayConfig['credentials_schema'] as $field => $schema)
      <div class="mb-3">
        <label class="form-label">{{ $schema['label'] }}</label>

        @if($schema['type'] === 'password')
        <input type="password" name="{{ $field }}" class="form-control"
          value="{{ old($field, $gatewayModel->credentials[$field] ?? '') }}" {{ $schema['required'] ? 'required' : ''
          }}>
        @elseif($schema['type'] === 'select')
        <select name="{{ $field }}" class="form-control">
          @foreach($schema['options'] as $option)
          <option value="{{ $option }}" {{ old($field, $gatewayModel->config[$field] ?? $schema['default'] ?? '') ==
            $option ? 'selected' : '' }}>
            {{ ucfirst($option) }}
          </option>
          @endforeach
        </select>
        @else
        <input type="text" name="{{ $field }}" class="form-control"
          value="{{ old($field, $gatewayModel->credentials[$field] ?? '') }}" {{ $schema['required'] ? 'required' : ''
          }}>
        @endif

        @error($field)
        <div class="text-danger">{{ $message }}</div>
        @enderror
      </div>
      @endforeach

      <button type="submit" class="btn btn-primary">Save Configuration</button>
      <a href="{{ route('tenant.gateways.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
  </div> --}}

</body>

</html>