<?php

declare(strict_types=1);

namespace Ttpryg\OrderEngine\Repositories;

use DateTimeImmutable;
use PDO;
use Ttpryg\OrderEngine\Contracts\OrderHistoryRepositoryInterface;
use Ttpryg\OrderEngine\Entities\OrderHistory;

class PdoOrderHistoryRepository implements OrderHistoryRepositoryInterface
{
    public function __construct(private readonly PDO $pdo) {}

    public function save(OrderHistory $history): void
    {
        $sql = 'INSERT INTO order_histories (id, order_id, action, from_status, to_status, reference_type, reference_id, note, actor_id, created_at) VALUES (:id, :order_id, :action, :from_status, :to_status, :reference_type, :reference_id, :note, :actor_id, :created_at)';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $history->id,
            'order_id' => $history->orderId,
            'action' => $history->action->value,
            'from_status' => $history->fromStatus?->value,
            'to_status' => $history->toStatus?->value,
            'reference_type' => $history->referenceType,
            'reference_id' => $history->referenceId,
            'note' => $history->note,
            'actor_id' => $history->actorId,
            'created_at' => $history->createdAt->format('Y-m-d H:i:s'),
        ]);
    }

    public function findByOrderId(string $orderId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM order_histories WHERE order_id = :order_id ORDER BY created_at ASC');
        $stmt->execute(['order_id' => $orderId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map($this->mapToEntity(...), $rows);
    }

    private function mapToEntity(array $row): OrderHistory
    {
        return new OrderHistory(
            id: (string) $row['id'],
            orderId: (string) $row['order_id'],
            action: (string) $row['action'],
            fromStatus: ! empty($row['from_status']) ? (string) $row['from_status'] : null,
            toStatus: ! empty($row['to_status']) ? (string) $row['to_status'] : null,
            referenceType: $row['reference_type'] ?? null,
            referenceId: $row['reference_id'] ?? null,
            note: $row['note'] ?? null,
            actorId: $row['actor_id'] ?? null,
            createdAt: ! empty($row['created_at']) ? new DateTimeImmutable($row['created_at']) : null
        );
    }
}
