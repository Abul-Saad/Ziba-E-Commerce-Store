@foreach($categories as $category)
    <h2>{{ $category->name }}</h2>
    <ul>
        @foreach($category->products as $product)
            <li>{{ $product->name }} - ₹{{ $product->current_price }}</li>
        @endforeach
    </ul>
@endforeach
