class LabOrder {
  final int id;
  final int photographerId;
  final double total;
  final String status;
  final DateTime createdAt;
  final String? photographerName;
  final String? studioName;
  final String? notes;
  final List<OrderItem>? items;

  LabOrder({
    required this.id,
    required this.photographerId,
    required this.total,
    required this.status,
    required this.createdAt,
    this.photographerName,
    this.studioName,
    this.notes,
    this.items,
  });

  factory LabOrder.fromJson(Map<String, dynamic> json) {
    return LabOrder(
      id: json['id'] is String ? int.parse(json['id']) : (json['id'] ?? 0),
      photographerId: json['photographer_id'] != null
          ? (json['photographer_id'] is String ? int.parse(json['photographer_id']) : json['photographer_id'])
          : 0,
      total: (json['total'] != null)
          ? (json['total'] is String ? double.parse(json['total']) : (json['total'] as num).toDouble())
          : 0.0,
      status: json['status'] ?? 'pending',
      createdAt: json['created_at'] != null 
          ? DateTime.parse(json['created_at']) 
          : DateTime.now(),
      photographerName: json['photographer_name'],
      studioName: json['studio_name'],
      notes: json['notes'],
      items: json['items'] != null
          ? (json['items'] as List).map((i) => OrderItem.fromJson(i)).toList()
          : null,
    );
  }
}

class OrderItem {
  final int id;
  final String productName;
  final int quantity;
  final double subtotal;
  final String? category;

  OrderItem({
    required this.id,
    required this.productName,
    required this.quantity,
    required this.subtotal,
    this.category,
  });

  factory OrderItem.fromJson(Map<String, dynamic> json) {
    return OrderItem(
      id: json['id'] is String ? int.parse(json['id']) : (json['id'] ?? 0),
      productName: json['product_name'] ?? 'Product',
      quantity: json['quantity'] != null
          ? (json['quantity'] is String ? int.parse(json['quantity']) : json['quantity'])
          : 1,
      subtotal: (json['subtotal'] != null)
          ? (json['subtotal'] is String ? double.parse(json['subtotal']) : (json['subtotal'] as num).toDouble())
          : 0.0,
      category: json['category'],
    );
  }
}
