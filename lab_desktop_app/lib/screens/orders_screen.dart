import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import 'package:lab_desktop_app/providers/order_provider.dart';
import 'package:lab_desktop_app/models/order_model.dart';

class OrdersScreen extends StatefulWidget {
  const OrdersScreen({super.key});

  @override
  State<OrdersScreen> createState() => _OrdersScreenState();
}

class _OrdersScreenState extends State<OrdersScreen> {
  String? _selectedStatus;
  final DateFormat _dateFormat = DateFormat('dd MMM yyyy, hh:mm a');
  final NumberFormat _currencyFormat = NumberFormat.currency(locale: 'en_IN', symbol: '₹');

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<OrderProvider>().fetchOrders();
    });
  }

  @override
  Widget build(BuildContext context) {
    final orderProvider = context.watch<OrderProvider>();

    return Padding(
      padding: const EdgeInsets.all(32.0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Order Management',
                    style: TextStyle(fontSize: 28, fontWeight: FontWeight.bold, color: Colors.white),
                  ),
                  Text(
                    'Review and process incoming photobook orders.',
                    style: TextStyle(color: Colors.grey),
                  ),
                ],
              ),
              _buildFilters(),
            ],
          ),
          const SizedBox(height: 32),
          if (orderProvider.isLoading)
            const Expanded(child: Center(child: CircularProgressIndicator(color: Color(0xFFC9A227))))
          else if (orderProvider.errorMessage != null)
            Center(child: Text(orderProvider.errorMessage!, style: const TextStyle(color: Colors.redAccent)))
          else if (orderProvider.orders.isEmpty)
            const Expanded(child: Center(child: Text('No orders found.', style: TextStyle(color: Colors.grey))))
          else
            Expanded(
              child: Container(
                decoration: BoxDecoration(
                  color: const Color(0xFF1A1D27),
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: Colors.white.withOpacity(0.05)),
                ),
                child: ClipRRect(
                  borderRadius: BorderRadius.circular(16),
                  child: SingleChildScrollView(
                    child: DataTable(
                      headingRowColor: MaterialStateProperty.all(Colors.white.withOpacity(0.02)),
                      dataRowMaxHeight: 70,
                      columns: const [
                        DataColumn(label: Text('ORDER ID', style: TextStyle(color: Colors.grey, fontSize: 12))),
                        DataColumn(label: Text('DATE', style: TextStyle(color: Colors.grey, fontSize: 12))),
                        DataColumn(label: Text('STUDIO / NAME', style: TextStyle(color: Colors.grey, fontSize: 12))),
                        DataColumn(label: Text('AMOUNT', style: TextStyle(color: Colors.grey, fontSize: 12))),
                        DataColumn(label: Text('STATUS', style: TextStyle(color: Colors.grey, fontSize: 12))),
                        DataColumn(label: Text('ACTIONS', style: TextStyle(color: Colors.grey, fontSize: 12))),
                      ],
                      rows: orderProvider.orders.map((order) => _buildOrderRow(order)).toList(),
                    ),
                  ),
                ),
              ),
            ),
        ],
      ),
    );
  }

  DataRow _buildOrderRow(LabOrder order) {
    return DataRow(cells: [
      DataCell(Text('#${order.id}', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold))),
      DataCell(Text(_dateFormat.format(order.createdAt), style: const TextStyle(color: Colors.grey, fontSize: 13))),
      DataCell(Column(
        mainAxisAlignment: MainAxisAlignment.center,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(order.studioName ?? 'Unknown Studio', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
          Text(order.photographerName ?? '-', style: const TextStyle(color: Colors.grey, fontSize: 11)),
        ],
      )),
      DataCell(Text(_currencyFormat.format(order.total), style: const TextStyle(color: Color(0xFFC9A227), fontWeight: FontWeight.bold))),
      DataCell(_buildStatusBadge(order.status)),
      DataCell(Row(
        children: [
          IconButton(
            icon: const Icon(Icons.remove_red_eye_outlined, size: 18, color: Colors.blueAccent),
            onPressed: () => _showOrderDetails(order),
            tooltip: 'View Details',
          ),
          IconButton(
            icon: const Icon(Icons.edit_note, size: 20, color: Colors.grey),
            onPressed: () => _showStatusPicker(order),
            tooltip: 'Update Status',
          ),
        ],
      )),
    ]);
  }

  Widget _buildStatusBadge(String status) {
    Color color;
    switch (status.toLowerCase()) {
      case 'pending': color = Colors.orangeAccent; break;
      case 'paid': color = Colors.tealAccent; break;
      case 'processing': color = Colors.blueAccent; break;
      case 'shipped': color = Colors.purpleAccent; break;
      case 'delivered': color = Colors.greenAccent; break;
      case 'cancelled': color = Colors.redAccent; break;
      default: color = Colors.grey;
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(
        color: color.withOpacity(0.1),
        borderRadius: BorderRadius.circular(4),
        border: Border.all(color: color.withOpacity(0.5)),
      ),
      child: Text(
        status.toUpperCase(),
        style: TextStyle(color: color, fontSize: 10, fontWeight: FontWeight.bold, letterSpacing: 0.5),
      ),
    );
  }

  Widget _buildFilters() {
    return Row(
      children: [
        _statusFilterButton('All', null),
        const SizedBox(width: 8),
        _statusFilterButton('Pending', 'pending'),
        const SizedBox(width: 8),
        _statusFilterButton('Paid', 'paid'),
        const SizedBox(width: 8),
        _statusFilterButton('Processing', 'processing'),
        const SizedBox(width: 8),
        _statusFilterButton('Shipped', 'shipped'),
      ],
    );
  }

  Widget _statusFilterButton(String label, String? status) {
    bool isSelected = _selectedStatus == status;
    return ChoiceChip(
      label: Text(label),
      selected: isSelected,
      onSelected: (val) {
        setState(() => _selectedStatus = status);
        context.read<OrderProvider>().fetchOrders(status: status);
      },
      selectedColor: const Color(0xFFC9A227).withOpacity(0.2),
      labelStyle: TextStyle(color: isSelected ? const Color(0xFFC9A227) : Colors.grey, fontSize: 12),
      backgroundColor: Colors.transparent,
      side: BorderSide(color: isSelected ? const Color(0xFFC9A227) : Colors.white.withOpacity(0.1)),
      showCheckmark: false,
    );
  }

  void _showOrderDetails(LabOrder order) async {
    final details = await context.read<OrderProvider>().getOrderDetail(order.id);
    if (!mounted || details == null) return;

    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        backgroundColor: const Color(0xFF1A1D27),
        title: Text('Order #${order.id} Details', style: const TextStyle(color: Colors.white)),
        content: SizedBox(
          width: 600,
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              if (details.items != null)
                ...details.items!.map((item) => ListTile(
                  title: Text(item.productName, style: const TextStyle(color: Colors.white)),
                  subtitle: Text('Qty: ${item.quantity} | ${item.category}', style: const TextStyle(color: Colors.grey)),
                  trailing: Text(_currencyFormat.format(item.subtotal), style: const TextStyle(color: Colors.white)),
                )),
              if (order.notes != null && order.notes!.isNotEmpty) ...[
                const Divider(color: Colors.white10),
                ListTile(
                  title: const Text('Photographer Notes', style: TextStyle(color: Colors.grey, fontSize: 12)),
                  subtitle: Text(order.notes!, style: const TextStyle(color: Colors.white70)),
                ),
              ],
              if (details.driveLink != null && details.driveLink!.isNotEmpty) ...[
                const Divider(color: Colors.white10),
                ListTile(
                  title: const Text('Design Files Link', style: TextStyle(color: Colors.grey, fontSize: 12)),
                  subtitle: SelectableText(details.driveLink!, style: const TextStyle(color: Colors.lightBlueAccent, decoration: TextDecoration.underline)),
                ),
              ],
              if (details.shippingAddress != null && details.shippingAddress!.isNotEmpty) ...[
                const Divider(color: Colors.white10),
                ListTile(
                  title: const Text('Shipping Address', style: TextStyle(color: Colors.grey, fontSize: 12)),
                  subtitle: Text(details.shippingAddress!, style: const TextStyle(color: Colors.white70)),
                ),
              ],
            ],
          ),
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context), child: const Text('Close')),
        ],
      ),
    );
  }

  void _showStatusPicker(LabOrder order) {
    final statuses = ['pending', 'paid', 'processing', 'shipped', 'delivered', 'cancelled'];
    showDialog(
      context: context,
      builder: (context) => SimpleDialog(
        backgroundColor: const Color(0xFF1A1D27),
        title: const Text('Update Order Status', style: TextStyle(color: Colors.white)),
        children: statuses.map((s) => SimpleDialogOption(
          onPressed: () async {
            Navigator.pop(context);
            final success = await context.read<OrderProvider>().updateOrderStatus(order.id, s);
            if (success && mounted) {
              ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Order #${order.id} updated to $s')));
            }
          },
          child: Text(s.toUpperCase(), style: const TextStyle(color: Colors.white70)),
        )).toList(),
      ),
    );
  }
}
