<?php
declare(strict_types = 1);

namespace skymin\asyncqueue;

use pocketmine\Server;
use pocketmine\scheduler\AsyncTask;

use Closure;
use function spl_object_id;

final class AsyncQueue{
	
	/** @var AsyncTask[] */
	private static array $queue = [];
	
	/** @phpstan-param Closure(AsyncTask<mixed> $task): void $callback */
	public static function submit(AsyncTask $task, ?Closure $callBack = null) : void{
		Server::getInstance()->getAsyncPool()->submitTask($task);
		$id = spl_object_id($task);
		if(isset(self::$queue[$id])){
			unset(self::$queue[$id]);
		}
		if($callBack !== null) {
			self::$queue[$id] = $callBack;
		}
	}
	
	public static function callBack(AsyncTask $task) : void{
		$id = spl_object_id($task);
		if(isset(self::$queue[$id])){;
			self::$queue[$id]($task);
		}
	}
	
	public static function wait(AsyncTask $task) : void{
		if($task->isFinished()) return;
		while(!$task->isFinished()){
			unsleep(1000);
		}
	}
	
}
