import 'package:lab_desktop_app/models/product_model.dart';

class OrderItem {
  final int id;
  final int? productId;
  final String productName;
  final String? category;
  final int quantity;
  final double price;
  final double subtotal;
  final String? unit;

  OrderItem({
    required this.id,
    this.productId,
    required this.productName,
    this.category,
    required this.quantity,
    required this.price,
    required this.subtotal,
    this.unit,
  });

  factory OrderItem.fromJson(Map<String, dynamic> json) {
    return OrderItem(
      id: json['id'] is String ? int.parse(json['id']) : json['id'],
      productId: json['product_id'] != null 
          ? (json['product_id'] is String ? int.parse(json['product_id']) : json['product_id'])
          : null,
      productName: json['product_name'] ?? '',
      category: json['category'],
      quantity: json['quantity'] is String ? int.parse(json['quantity']) : json['quantity'],
      price: (json['price'] != null) ? (json['price'] is String ? double.parse(json['price']) : (json['price'] as num).toDouble()) : 0.0,
      subtotal: (json['subtotal'] != null) ? (json['subtotal'] is String ? double.parse(json['subtotal']) : (json['subtotal'] as num).toDouble()) : 0.0,
      unit: json['unit'],
    );
  }
}

class LabOrder {
  final int id;
  final int photographerId;
  final String? photographerName;
  final String? studioName;
  final double total;
  final String status;
  final String? notes;
  final String? adminNotes;
  final DateTime createdAt;
  final List<OrderItem>? items;

  LabOrder({
    required this.id,
    required this.photographerId,
    this.photographerName,
    this.studioName,
    required this.total,
    required this.status,
    this.notes,
    this.adminNotes,
    required this.createdAt,
    this.items,
  });

  factory LabOrder.fromJson(Map<String, dynamic> json) {
    return LabOrder(
      id: json['id'] is String ? int.parse(json['id']) : (json['id'] ?? 0),
      photographerId: json['photographer_id'] != null
          ? (json['photographer_id'] is String ? int.parse(json['photographer_id']) : json['photographer_id'])
          : 0,
      photographerName: json['photographer_name'] ?? json['photographer'] ?? 'Unknown',
      studioName: json['studio_name'],
      total: json['total'] is String ? double.parse(json['total']) : (json['total'] as num?)?.toDouble() ?? 0.0,
      status: json['status'] ?? 'pending',
      notes: json['notes'],
      adminNotes: json['admin_notes'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : DateTime.now(),
      items: json['items'] != null
          ? (json['items'] as List).map((i) => OrderItem.fromJson(i)).toList()
          : null,
    );
  }
}
