class Product {
  final int id;
  final String name;
  final String category;
  final String? description;
  final double price;
  final String? unit;
  final int minQty;
  final bool active;
  final String? imageUrl;

  Product({
    required this.id,
    required this.name,
    required this.category,
    this.description,
    required this.price,
    this.unit,
    required this.minQty,
    this.active = true,
    this.imageUrl,
  });

  factory Product.fromJson(Map<String, dynamic> json) {
    return Product(
      id: json['id'],
      name: json['name'],
      category: json['category'],
      description: json['description'],
      price: double.parse(json['price'].toString()),
      unit: json['unit'],
      minQty: int.parse((json['min_qty'] ?? 1).toString()),
      active: json['active'] == 1 || json['active'] == true,
      imageUrl: json['image'],
    );
  }
}
