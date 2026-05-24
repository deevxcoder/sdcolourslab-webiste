class Product {
  final int id;
  final String name;
  final String? description;
  final String category;
  final double price;
  final String unit;
  final int minQty;
  final String? image;
  final bool active;

  Product({
    required this.id,
    required this.name,
    this.description,
    required this.category,
    required this.price,
    required this.unit,
    required this.minQty,
    this.image,
    this.active = true,
  });

  factory Product.fromJson(Map<String, dynamic> json) {
    return Product(
      id: json['id'] is String ? int.parse(json['id']) : (json['id'] ?? 0),
      name: json['name'] ?? 'Unknown Product',
      description: json['description'],
      category: json['category'] ?? 'General',
      price: json['price'] is String ? double.parse(json['price']) : (json['price'] as num?)?.toDouble() ?? 0.0,
      unit: json['unit'] ?? 'pcs',
      minQty: json['min_qty'] != null ? (json['min_qty'] is String ? int.parse(json['min_qty']) : json['min_qty']) : 1,
      image: json['image'],
      active: json['active'] == true || json['active'] == 1 || json['active'] == "1",
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'description': description,
      'category': category,
      'price': price,
      'unit': unit,
      'min_qty': minQty,
      'image': image,
      'active': active,
    };
  }
}
