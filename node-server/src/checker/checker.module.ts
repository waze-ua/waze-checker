import { HttpModule } from '@nestjs/axios';
import { Module } from '@nestjs/common';
import { KeyValueModule } from 'src/keyValue/keyValue.module';
import { CheckerController } from './checker.controller';
import { CheckerService } from './checker.service';

@Module({
  imports: [HttpModule, KeyValueModule],
  controllers: [CheckerController],
  providers: [CheckerService],
})
export class CheckerModule {}
