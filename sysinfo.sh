#!/bin/bash

# -------------------------------------------
# Simple Linux System Information Collector
# Works on Ubuntu & all Linux-based systems
# -------------------------------------------

OUTPUT_FILE="system_diagnostics_$(date +%Y%m%d_%H%M%S).txt"

echo "Collecting system information..."
echo "Output will be saved to: $OUTPUT_FILE"
echo "-------------------------------------------" > "$OUTPUT_FILE"

# Basic System Info
echo "### Basic System Information ###" >> "$OUTPUT_FILE"
echo "Hostname: $(hostname)" >> "$OUTPUT_FILE"
echo "OS: $(grep PRETTY_NAME /etc/os-release | cut -d= -f2- | tr -d '\"')" >> "$OUTPUT_FILE"
echo "Kernel Version: $(uname -r)" >> "$OUTPUT_FILE"
echo "" >> "$OUTPUT_FILE"

# Hardware Info
echo "### CPU Information ###" >> "$OUTPUT_FILE"
lscpu >> "$OUTPUT_FILE" 2>/dev/null
echo "" >> "$OUTPUT_FILE"

echo "### Memory Information ###" >> "$OUTPUT_FILE"
free -h >> "$OUTPUT_FILE"
echo "" >> "$OUTPUT_FILE"

echo "### Disk Information ###" >> "$OUTPUT_FILE"
df -h >> "$OUTPUT_FILE"
echo "" >> "$OUTPUT_FILE"

# Network Info
echo "### Network Information ###" >> "$OUTPUT_FILE"
ip -brief address >> "$OUTPUT_FILE" 2>/dev/null
echo "" >> "$OUTPUT_FILE"

echo "### Default Gateway ###" >> "$OUTPUT_FILE"
ip route show default >> "$OUTPUT_FILE" 2>/dev/null
echo "" >> "$OUTPUT_FILE"

# Services / Processes
echo "### Top Running Processes ###" >> "$OUTPUT_FILE"
ps -eo pid,ppid,cmd,%mem,%cpu --sort=-%cpu | head -20 >> "$OUTPUT_FILE"
echo "" >> "$OUTPUT_FILE"

# System Logs (last 50)
echo "### Recent System Logs (Last 50 Lines) ###" >> "$OUTPUT_FILE"
journalctl -n 50 --no-pager >> "$OUTPUT_FILE" 2>/dev/null
echo "" >> "$OUTPUT_FILE"

echo "### Uptime ###" >> "$OUTPUT_FILE"
uptime >> "$OUTPUT_FILE"
echo "" >> "$OUTPUT_FILE"

echo "Diagnostics collection complete!"
echo "Saved as: $OUTPUT_FILE"
