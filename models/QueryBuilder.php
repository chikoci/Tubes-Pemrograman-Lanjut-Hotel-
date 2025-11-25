<?php

/**
 * Class RawExpression
 * Digunakan untuk memasukkan query mentah (seperti NOW()) ke dalam builder.
 */
class RawExpression {
    protected $value;
    public function __construct($value) { $this->value = $value; }
    public function getValue() { return $this->value; }
}

/**
 * Class QueryBuilder
 * Query Builder untuk memudahkan operasi database dengan fluent interface
 */
class QueryBuilder {
    protected $db;
    protected $table;
    protected $selects = ['*'];
    protected $joins = [];
    protected $wheres = [];
    protected $orderBy = [];
    protected $limit;
    protected $offset;
    
    protected $bindings = [];
    protected $binding_counter = 0;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Memulai query baru (reset state)
     */
    protected function newQuery(): self {
        $this->selects = ['*'];
        $this->joins = [];
        $this->wheres = [];
        $this->orderBy = [];
        $this->limit = null;
        $this->offset = null;
        $this->bindings = [];
        $this->binding_counter = 0;
        return $this;
    }

    /**
     * Set tabel yang akan digunakan
     */
    public function table(string $table): self {
        $this->newQuery(); 
        $this->table = $table;
        return $this;
    }
    
    /**
     * Membuat raw expression
     */
    public function raw(string $value): RawExpression {
        return new RawExpression($value);
    }

    /**
     * Set kolom yang akan di-select
     */
    public function select(array $columns = ['*']): self {
        $this->selects = $columns;
        return $this;
    }

    /**
     * JOIN tabel
     */
    public function join(string $table, string $first, string $operator, string $second): self {
        $this->joins[] = "JOIN $table ON $first $operator $second";
        return $this;
    }

    /**
     * LEFT JOIN tabel
     */
    public function leftJoin(string $table, string $first, string $operator, string $second): self {
        $this->joins[] = "LEFT JOIN $table ON $first $operator $second";
        return $this;
    }
    
    /**
     * WHERE clause
     */
    public function where(string $column, string $operator, $value): self {
        $placeholder = $this->addBinding($value);
        $this->wheres[] = [
            'type' => 'AND',
            'query' => "$column $operator $placeholder"
        ];
        return $this;
    }

    /**
     * OR WHERE clause
     */
    public function orWhere(string $column, string $operator, $value): self {
        $placeholder = $this->addBinding($value);
        $this->wheres[] = [
            'type' => 'OR',
            'query' => "$column $operator $placeholder"
        ];
        return $this;
    }

    /**
     * WHERE NULL
     */
    public function whereNull(string $column, string $type = 'AND'): self {
        $this->wheres[] = ['type' => $type, 'query' => "$column IS NULL"];
        return $this;
    }
    
    /**
     * WHERE NOT NULL
     */
    public function whereNotNull(string $column, string $type = 'AND'): self {
        $this->wheres[] = ['type' => $type, 'query' => "$column IS NOT NULL"];
        return $this;
    }
    
    /**
     * WHERE IN
     */
    public function whereIn(string $column, array $values, string $type = 'AND'): self {
        if (empty($values)) return $this;
        $placeholders = implode(',', array_map([$this, 'addBinding'], $values));
        $this->wheres[] = [
            'type' => $type,
            'query' => "$column IN ($placeholders)"
        ];
        return $this;
    }

    /**
     * Set binding counter (untuk sub-query)
     */
    public function setBindingCounter(int $count): void {
        $this->binding_counter = $count;
    }
    
    /**
     * Get binding counter
     */
    public function getBindingCounter(): int {
        return $this->binding_counter;
    }
    
    /**
     * WHERE dengan grouping (menggunakan callback)
     */
    public function whereGrouped(callable $callback, string $type = 'AND'): self {
        $query = new self($this->db);
        $query->setBindingCounter($this->binding_counter);
        
        $callback($query);
        
        if (!empty($query->wheres)) {
            $sql = $this->compileWheres($query->wheres, true);
            $this->wheres[] = ['type' => $type, 'query' => "($sql)"];
            $this->bindings = array_merge($this->bindings, $query->bindings);
            $this->binding_counter = $query->getBindingCounter(); 
        }
        return $this;
    }
    
    /**
     * ORDER BY
     */
    public function orderBy(string $column, string $direction = 'ASC'): self {
        $this->orderBy[] = "$column $direction";
        return $this;
    }
    
    /**
     * LIMIT
     */
    public function limit(int $count): self {
        $this->limit = $count;
        return $this;
    }

    /**
     * OFFSET
     */
    public function offset(int $count): self {
        $this->offset = $count;
        return $this;
    }
    
    /**
     * Tambahkan binding value
     */
    protected function addBinding($value): string {
        $this->binding_counter++; 
        $placeholder = ':b' . $this->binding_counter; 
        $this->bindings[$placeholder] = $value;
        return $placeholder;
    }
    
    /**
     * Compile WHERE clauses
     */
    protected function compileWheres(array $wheres, bool $isGrouped = false): string {
        $sql = '';
        foreach ($wheres as $i => $where) {
            $prefix = ($i == 0) ? '' : $where['type'] . ' ';
            if ($isGrouped && $i == 0) $prefix = ''; 
            $sql .= $prefix . $where['query'] . ' ';
        }
        return trim($sql);
    }
    
    /**
     * Build SELECT query
     */
    protected function buildSelectQuery(): string {
        $sql = "SELECT " . implode(', ', $this->selects);
        $sql .= " FROM " . $this->table;
        
        if (!empty($this->joins)) {
            $sql .= " " . implode(' ', $this->joins);
        }
        
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . $this->compileWheres($this->wheres);
        }
        
        if (!empty($this->orderBy)) {
            $sql .= " ORDER BY " . implode(', ', $this->orderBy);
        }
        
        if ($this->limit !== null) {
            $sql .= " LIMIT " . $this->limit;
        }
        
        if ($this->offset !== null) {
            $sql .= " OFFSET " . $this->offset;
        }
        
        return $sql;
    }
    
    /**
     * Execute SELECT dan return array hasil
     */
    public function get(): array {
        $sql = $this->buildSelectQuery();
        $stmt = $this->db->prepare($sql);
        $stmt->execute($this->bindings);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Execute SELECT dan return single row
     */
    public function first(): ?array {
        $this->limit(1);
        $sql = $this->buildSelectQuery();
        $stmt = $this->db->prepare($sql);
        $stmt->execute($this->bindings);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * COUNT query
     */
    public function count(string $column = '*'): int {
        $this->selects = ["COUNT($column) as total"];
        $this->orderBy = [];
        $this->limit = null;
        $this->offset = null;
        
        $sql = $this->buildSelectQuery();
        $stmt = $this->db->prepare($sql);
        $stmt->execute($this->bindings);
        return (int) $stmt->fetchColumn();
    }
    
    /**
     * INSERT data
     */
    public function insert(array $data): bool {
        $columns = implode(', ', array_keys($data));
        $placeholders = [];
        $bindings = [];
        
        foreach ($data as $key => $value) {
            $placeholder = ':' . $key;
            $placeholders[] = $placeholder;
            $bindings[$placeholder] = ($value instanceof RawExpression) ? $value->getValue() : $value;
        }
        
        $placeholdersStr = implode(', ', $placeholders);
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholdersStr)";
        
        // Handle raw expressions
        foreach ($bindings as $key => $value) {
            if ($value instanceof RawExpression) {
                $sql = str_replace($key, $value->getValue(), $sql);
                unset($bindings[$key]);
            }
        }
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($bindings);
    }

    /**
     * INSERT dan return ID
     */
    public function insertGetId(array $data): ?int {
        if ($this->insert($data)) {
            return (int) $this->db->lastInsertId();
        }
        return null;
    }
    
    /**
     * UPDATE data
     */
    public function update(array $data): bool {
        $setParts = [];
        $bindings = [];
        
        foreach ($data as $key => $value) {
            $placeholder = ':u_' . $key; 
            if ($value instanceof RawExpression) {
                $setParts[] = "$key = " . $value->getValue();
            } elseif ($value === null) {
                $setParts[] = "$key = NULL";
            } else {
                $setParts[] = "$key = $placeholder";
                $bindings[$placeholder] = $value;
            }
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts);
        
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . $this->compileWheres($this->wheres);
        }

        $allBindings = array_merge($bindings, $this->bindings);
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($allBindings);
    }
    
    /**
     * DELETE data
     */
    public function delete(): bool {
        $sql = "DELETE FROM {$this->table}";
        
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . $this->compileWheres($this->wheres);
        }
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($this->bindings);
    }
}
?>
