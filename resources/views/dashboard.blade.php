<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>

<body>

<h1>Database Preview</h1>


<h2>Categories</h2>

<table border="1">

<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Slug</th>
    <th>Image</th>
</tr>

@foreach($categories as $category)

<tr>

<td>{{ $category->id }}</td>

<td>
    {{ json_encode($category->name, JSON_UNESCAPED_UNICODE) }}
</td>

<td>
    {{ $category->slug }}
</td>

<td>
    {{ $category->image }}
</td>

</tr>

@endforeach

</table>



<br><br>


<h2>Products</h2>

<table border="1">

<tr>

<th>ID</th>
<th>Category ID</th>
<th>Name</th>
<th>Slug</th>
<th>SKU</th>
<th>Price</th>
<th>Discount</th>
<th>Stock</th>
<th>Status</th>

</tr>


@foreach($products as $product)

<tr>

<td>{{ $product->id }}</td>

<td>{{ $product->category_id }}</td>

<td>
{{ json_encode($product->name, JSON_UNESCAPED_UNICODE) }}
</td>

<td>{{ $product->slug }}</td>

<td>{{ $product->sku }}</td>

<td>{{ $product->base_price }}</td>

<td>{{ $product->discount }}</td>

<td>{{ $product->stock }}</td>

<td>{{ $product->status }}</td>


</tr>

@endforeach

</table>



<br><br>


<h2>Product Images</h2>

<table border="1">

<tr>
<th>ID</th>
<th>Product ID</th>
<th>Image</th>
<th>Main</th>
<th>Sort</th>
</tr>


@foreach($images as $image)

<tr>

<td>{{ $image->id }}</td>

<td>{{ $image->product_id }}</td>

<td>{{ $image->image }}</td>

<td>{{ $image->is_main }}</td>

<td>{{ $image->sort_order }}</td>

</tr>

@endforeach


</table>



<br><br>


<h2>Product Units</h2>

<table border="1">

<tr>

<th>ID</th>
<th>Product ID</th>
<th>Unit Name</th>
<th>Price</th>
<th>Stock</th>

</tr>


@foreach($units as $unit)

<tr>

<td>{{ $unit->id }}</td>

<td>{{ $unit->product_id }}</td>

<td>
{{ json_encode($unit->unit_name, JSON_UNESCAPED_UNICODE) }}
</td>

<td>{{ $unit->price }}</td>

<td>{{ $unit->stock }}</td>

</tr>


@endforeach


</table>


</body>
</html>