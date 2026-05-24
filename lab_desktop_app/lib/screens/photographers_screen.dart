import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:lab_desktop_app/providers/photographer_provider.dart';
import 'package:lab_desktop_app/models/user_model.dart';

class PhotographersScreen extends StatefulWidget {
  const PhotographersScreen({super.key});

  @override
  State<PhotographersScreen> createState() => _PhotographersScreenState();
}

class _PhotographersScreenState extends State<PhotographersScreen> {
  String? _statusFilter;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<PhotographerProvider>().fetchPhotographers();
    });
  }

  @override
  Widget build(BuildContext context) {
    final provider = context.watch<PhotographerProvider>();

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
                    'Photographer Management',
                    style: TextStyle(fontSize: 28, fontWeight: FontWeight.bold, color: Colors.white),
                  ),
                  Text(
                    'Manage registrations and studio approvals.',
                    style: TextStyle(color: Colors.grey),
                  ),
                ],
              ),
              _buildFilters(),
            ],
          ),
          const SizedBox(height: 32),
          if (provider.isLoading)
            const Expanded(child: Center(child: CircularProgressIndicator(color: Color(0xFFC9A227))))
          else if (provider.errorMessage != null)
            Center(child: Text(provider.errorMessage!, style: const TextStyle(color: Colors.redAccent)))
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
                      dataRowMaxHeight: 60,
                      columns: const [
                        DataColumn(label: Text('ID', style: TextStyle(color: Colors.grey, fontSize: 12))),
                        DataColumn(label: Text('STUDIO / NAME', style: TextStyle(color: Colors.grey, fontSize: 12))),
                        DataColumn(label: Text('LOCATION', style: TextStyle(color: Colors.grey, fontSize: 12))),
                        DataColumn(label: Text('CONTACT', style: TextStyle(color: Colors.grey, fontSize: 12))),
                        DataColumn(label: Text('STATUS', style: TextStyle(color: Colors.grey, fontSize: 12))),
                        DataColumn(label: Text('ACTIONS', style: TextStyle(color: Colors.grey, fontSize: 12))),
                      ],
                      rows: provider.photographers.map((u) => _buildUserRow(u)).toList(),
                    ),
                  ),
                ),
              ),
            ),
        ],
      ),
    );
  }

  DataRow _buildUserRow(User user) {
    return DataRow(cells: [
      DataCell(Text('#${user.id}', style: const TextStyle(color: Colors.grey))),
      DataCell(Column(
        mainAxisAlignment: MainAxisAlignment.center,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(user.studioName ?? '-', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
          Text(user.name, style: const TextStyle(color: Colors.grey, fontSize: 11)),
        ],
      )),
      DataCell(Text(user.city ?? 'Unknown', style: const TextStyle(color: Colors.white70))),
      DataCell(Text(user.phone ?? user.email, style: const TextStyle(color: Colors.white70, fontSize: 12))),
      DataCell(_buildStatusBadge(user.status)),
      DataCell(Row(
        children: [
          if (user.status == 'pending') ...[
            IconButton(
              icon: const Icon(Icons.check_circle_outline, color: Colors.greenAccent, size: 20),
              onPressed: () => _updateStatus(user, 'approved'),
              tooltip: 'Approve',
            ),
            IconButton(
              icon: const Icon(Icons.cancel_outlined, color: Colors.redAccent, size: 20),
              onPressed: () => _updateStatus(user, 'rejected'),
              tooltip: 'Reject',
            ),
          ] else
            IconButton(
              icon: const Icon(Icons.history, color: Colors.grey, size: 18),
              onPressed: () => _updateStatus(user, 'pending'),
              tooltip: 'Reset to Pending',
            ),
        ],
      )),
    ]);
  }

  Widget _buildStatusBadge(String status) {
    Color color;
    switch (status) {
      case 'approved': color = Colors.greenAccent; break;
      case 'rejected': color = Colors.redAccent; break;
      default: color = Colors.orangeAccent;
    }
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
      decoration: BoxDecoration(
        color: color.withOpacity(0.1),
        borderRadius: BorderRadius.circular(4),
        border: Border.all(color: color.withOpacity(0.3)),
      ),
      child: Text(status.toUpperCase(), style: TextStyle(color: color, fontSize: 9, fontWeight: FontWeight.bold)),
    );
  }

  Widget _buildFilters() {
    return Row(
      children: [
        _filterChip('All', null),
        const SizedBox(width: 8),
        _filterChip('Pending', 'pending'),
        const SizedBox(width: 8),
        _filterChip('Approved', 'approved'),
      ],
    );
  }

  Widget _filterChip(String label, String? status) {
    bool isSelected = _statusFilter == status;
    return ChoiceChip(
      label: Text(label),
      selected: isSelected,
      onSelected: (val) {
        setState(() => _statusFilter = status);
        context.read<PhotographerProvider>().fetchPhotographers(status: status);
      },
      selectedColor: const Color(0xFFC9A227).withOpacity(0.2),
      labelStyle: TextStyle(color: isSelected ? const Color(0xFFC9A227) : Colors.grey, fontSize: 12),
      backgroundColor: Colors.transparent,
      side: BorderSide(color: isSelected ? const Color(0xFFC9A227) : Colors.white.withOpacity(0.1)),
      showCheckmark: false,
    );
  }

  void _updateStatus(User user, String status) async {
    final success = await context.read<PhotographerProvider>().updateStatus(user.id, status);
    if (success && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Photographer ${user.name} is now $status')),
      );
    }
  }
}
