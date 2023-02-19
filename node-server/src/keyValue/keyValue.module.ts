import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { KeyValueController } from './KeyValue.controller';
import { KeyValueService } from './keyValue.service';
import { KeyValueEntity } from './keyValue.entity';

@Module({
  imports: [TypeOrmModule.forFeature([KeyValueEntity])],
  exports: [KeyValueService],
  controllers: [KeyValueController],
  providers: [KeyValueService],
})
export class KeyValueModule {}
